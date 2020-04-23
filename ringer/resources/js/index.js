require('./bootstrap')

require('./global')

var routes = ['/login', '/register']
if (!routes.includes(location.pathname))
  require('./vue/app')
