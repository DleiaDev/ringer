// Pusher
import Pusher from 'pusher-js'
const pusher = new Pusher('df0217db3add0af08479', {
  cluster: 'eu',
  encrypted: true
})
// Pusher.logToConsole = true

// Laravel Echo
import Echo from 'laravel-echo'
window.Echo = new Echo({
  broadcaster: 'pusher',
  key: 'df0217db3add0af08479',
  cluster: 'eu',
  encrypted: true
})

// Vue
import Vue from 'vue'
window.Vue = Vue

// vForm
import { Form, HasError, AlertError } from 'vform'
window.Form = Form
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

// Variables
window.timezoneOffset = (new Date).getTimezoneOffset() / 60
Vue.prototype.$user = window.user
Vue.prototype.$progress = window.progress

// Sweetalert2
import Swal from 'sweetalert2';
window.Swal = Swal
window.toast = Swal.mixin({
  customClass: {container: 'swal-toast-container'},
  toast: true,
  position: 'top-start',
  showConfirmButton: false,
  timer: 4000
})

// Moment
import moment from 'moment'
window.moment = moment

// Filters
Vue.filter('shorten', text => {
  return text.length > 35 ? text.substring(0, 35) + '...' : text
})
Vue.filter('name', text => {
  return text.length > 15 ? text.substring(0, 15) + '...' : text
})
Vue.filter('relative', date => {
  var momentDate = moment(new Date(date))
  if (timezoneOffset > 0) momentDate.add(timezoneOffset, 'hours')
  else momentDate.subtract(timezoneOffset, 'hours')
  return momentDate.fromNow()
})
Vue.filter('time', date => {
  return moment(date).format('LT')
})

// App
import MainComp from './components/MainComp.vue'
new Vue({
  el: '#vue',
  render: h => h(MainComp)
})
