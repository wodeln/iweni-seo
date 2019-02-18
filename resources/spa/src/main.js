import { version } from '../package.json'
import { ENABLE_MOBLINK } from '@/constants/app'

import Vue from 'vue'

import 'github-markdown-css'
import './style/tsplus.less'
import './icons/iconfont.js' // from http://www.iconfont.cn h5 仓库
import './util/rem'
import './util/prototype' // 原型拓展

import Message from './plugins/message/'
import AsyncImage from './components/FeedCard/v-async-image.js'

import imgCropper from '@/plugins/imgCropper'
import lstore from '@/plugins/lstore/index.js'

import Ajax from './api/api.js'
import mixin from './mixin.js'
import * as filters from './filters.js'
import components from './components.js'

import store from './stores/'
import router from './routers/'
import App from './app'
import bus from './bus'
import './registerServiceWorker'

import * as WebIM from '@/vendor/easemob'
import VueSocketio from 'vue-socket.io';
export { version }

Vue.mixin(mixin)

components.forEach(component => {
  Vue.component(component.name, component)
})

Vue.config.productionTip = false

Vue.prototype.$http = Ajax
Vue.prototype.$Message = Message
Vue.prototype.$MessageBundle = filters.plusMessageFirst
Vue.prototype.$bus = bus

Vue.use(AsyncImage)
Vue.use(imgCropper)
Vue.use(lstore)

let user = lstore.getData('H5_CUR_USER');
let avatarUrl = user.avatar==null ? "":user.avatar.url
if(user){
    // let url = `http://is.com:3001?user_id=${user.id}&user_avatar=${avatarUrl}&user_nickname=${user.name}`;
    let url = `http://is.com:3001?user_id=${user.id}`;
    Vue.use(VueSocketio, url, store);
}
for (const k in filters) {
  Vue.filter(k, filters[k])
}
if (!window.initUrl) {
  window.initUrl = window.location.href.replace(/(\/$)/, '')
}

// 加载 moblink 用于引导打开 APP
if (ENABLE_MOBLINK) {
  window.addEventListener('load', () => {
    const key = process.env.VUE_APP_MOBLINK_KEY || ''
    const script = document.createElement('script')
    script.type = 'text/javascript'
    script.src = `//f.moblink.mob.com/3.0.1/moblink.js?appkey=${key}`
    script.onload = () => {
      // eslint-disable-next-line
      MobLink({ path: location.href })
    }
    document.querySelector('body').appendChild(script)
  })
}

/* eslint-disable no-new */
new Vue({
  store,
  router,
  /*created () {
    WebIM.openWebIM()
  },*/
  render: h => h(App),
}).$mount('#app')

// Exposed versions
/* eslint-disable no-console */
console.info(
  `%cWelcome to Plus(ThinkSNS+)! Release %c v${version} `,
  'color: #00A9E0;',
  'background:#35495e; padding: 1px; border-radius: 3px; color: #fff;'
)
console.info(
  `%cDevelopment by SlimKit Group 👉 https://github.com/slimkit \nApache-2.0 Licensed | Copyright © ${new Date().getFullYear()} Chengdu ZhiYiChuangXiang Technology Co., Ltd. All rights reserved.`,
  'color: #00A9E0;'
)
