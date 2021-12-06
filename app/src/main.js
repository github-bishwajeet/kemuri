import Vue from 'vue'
import App from './App.vue'
import './index.css'
import './assets/tailwind.css'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import { BootstrapVue } from 'bootstrap-vue'

Vue.use(BootstrapVue)
Vue.config.productionTip = false
Vue.config.publicPath = 'http://kemuri.test'

new Vue({
  render: h => h(App),
}).$mount('#app')
