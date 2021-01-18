<template>
  <div class="glance">
    <heading class="mb-6">{{ 
      __(dashboardLabel)
    }}</heading> 
    <loading-view :loading="loading"> 
      <card v-if="fields.length" class="flex flex-wrap"> 
        <div class="flex flex-wrap w-full px-8 border-b border-40" v-if="usesFilters">
          <h5 class="py-4">{{ __('Filtered By') }}:</h5>
          <span class="text-success p-4" v-for="(value, filter) in filters">{{ 
            findFieldByAttribute(filter).name
          }}<b class="text-danger cursor-pointer pl-1" @click="removeFilter(filter)">x</b></span>
        </div>
        <component
          v-for="(field, index) in fields"
          :key="index"
          :is="`form-`+field.component" 
          :field="field" 
          :class="fieldClasees(field)" 
          @hook:updated="handleChangeEvent(field)"
        />
      </card> 
    </loading-view>
  </div>
</template>

<script>
export default {
  props: ['dashboardName'],

  data: () => ({
    selected: 0,
    fields: [],
    filters: {},
    loading: true,
  }),
 
  async mounted() {
    await this.getFilters() 
  }, 

  methods: {
    async fetchDashboard() { 
      this.$parent.cards = []

      const {
        data: { label, cards },
      } = await Nova.request()
        .get(this.$parent.dashboardEndpoint, {
          params: this.filters
        })
        .catch(e => {
          this.$router.push({ name: '404' })
        })

      this.$parent.label = label
      this.$parent.cards = cards
    }, 

    async getFilters() {  
      await Nova.request()
        .get(this.fieldEndpoint, {
          params: this.filters
        })
        .then(({ data }) => {  
          this.loading = true
          this.$set(this.fields, [])  
          this.fields = data  

          this.fields.forEach(field => {
            if(this.filters[field.attribute] != undefined) field.value = this.filters[field.attribute]  
          })

          this.filters = _.pickBy(this.filters, (value, key) => { 
            return this.fields.find(field => field.attribute === key)
          }) 
        })
        .catch(e => {  
          Nova.error(__('Error')) 
        })  

        this.loading = false 
    },

    handleChange(field, value) { 
      value ? this.$set(this.filters, field.attribute, value) : this.$delete(this.filters, field.attribute, value) 

      this.getFilters()
      this.fetchDashboard()
    },

    handleChangeEvent(field) { 
      if(field.component == 'date-time') {
        var value = _.tap(new FormData(), formData => field.fill(formData)).get(field.attribute) 

        this.filters[field.attribute] == value ||
        this.handleChange(field, value)
      }
    },

    fieldClasees(field) {
      return _.tap(['field'], $classes => {
        $classes.push(field.width ? field.width : 'w-1/4')
      })
    },

    removeFilter(filter) {
      this.handleChange(this.findFieldByAttribute(filter), '')
    },

    findFieldByAttribute(attribute){
      return this.fields.find(field => field.attribute == attribute)
    }
  },

  computed: { 
    dashboardLabel() {
      return this.$parent.label
    },

    fieldEndpoint() {
      return this.$parent.dashboardEndpoint + `/filters`
    },

    availableFilters() {
      return this.fields.filter(field => ! this.filters[field.attribute])
    },

    usesFilters() {
      return Object.keys(this.filters).length > 0
    }
  },

  watch: {
    selected() {
      this.fetchDashboard()
    },

    dashboardName() {
      this.getFilters()
    },

    fields: function(filters, fields) { 
      fields.forEach(field => { 
        Nova.$off(field.attribute + '-change')
      })  
      filters.forEach(field => { 
        Nova.$on(field.attribute + '-change', (value) => this.handleChange(field, value))
      })
    }
  }
};
</script>

<style>
.glance ~ h1 {
  display: none !important;
}
.glance .field > div {
  width: 100% !important;
}
</style>
