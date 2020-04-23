// SASS
import '../sass/app.scss'

// jQuery
import $ from 'jquery'
window.$ = $

// Bootstrap
import 'bootstrap/dist/js/bootstrap.min.js'

// Popper
import Popper from 'popper.js'
window.Popper = Popper

// Axios
import axios from 'axios'
window.axios = axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
var token = document.head.querySelector('meta[name="csrf-token"]')
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content

// Mprogress
import Mprogress from 'mprogress/mprogress.min.js'
window.progress = new Mprogress({
  template: 3,
  parent: '#mprogress'
})
