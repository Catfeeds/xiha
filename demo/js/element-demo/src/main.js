import Vue from 'vue'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-default/index.css'
import App from './App.vue'
// import Test from './Test.vue'
// import { Button, Select, Notification } from 'element-ui'
// Vue.component(Button.name, Button)
// Vue.component(Select.name, Select)

// Vue.prototype.$notify = Notification

Vue.use(ElementUI)

new Vue({
  el: '#app',
  render: h => h(App)
  // render: h => h(Test)
})
