<template>
  <JoLoadMore
    key="find-ner"
    ref="loadmore"
    @onRefresh="onRefresh"
    @onLoadMore="onLoadMore"
  >{{getSex}}
    <UserItem
      v-for="user in users"
      :key="user.id"
      :user="user"
    />
  </JoLoadMore>
</template>

<script>
import { mapState } from 'vuex'
import { noop } from '@/util'
import UserItem from '@/components/UserItem.vue'
import { findNearbyUser } from '@/api/user.js'

export default {
  name: 'FindNer',
  components: {
    UserItem,
  },
  data () {
    return {
      users: [],
      page: 1,
      isActive: false,
      sex:this.$store.state.USER_SEX
    }
  },
  computed: {
    ...mapState(['POSITION']),
    lat () {
      return this.POSITION.lat
    },
    lng () {
      return this.POSITION.lng
    },
  },
  activated () {
    this.$refs.loadmore.beforeRefresh()
  },
  computed:{
    getSex(){
      this.sex=this.$store.state.USER_SEX;
    }
  },
  methods: {
    async formateUsers (users, callback = noop) {
      const userList = []
      for (let item of users) {
        userList.push(item.user_id)
      }
      const data = await this.$store.dispatch('user/getUserList', {
        id: userList.join(','),
      })
      // 修正数据顺序
      const sortedUsers = []
      for (const userId of userList) {
        const user = data.find(u => u.id === userId)
        user && sortedUsers.push(user)
      }
      this.users = sortedUsers
      const more = this.users.length < 15
      callback(more)
    },
    onRefresh (callback) {
      this.page = 1
      findNearbyUser({ lat: this.lat, lng: this.lng,sex:this.sex }, this.page).then(
        ({ data = [] }) => {
          this.users = []
          this.formateUsers(data, more => {
            this.$refs.loadmore.afterRefresh(more)
          })
          this.page = 2
        }
      )
    },
    onLoadMore (callback) {
      findNearbyUser({ lat: this.lat, lng: this.lng,sex:this.sex }, this.page).then(
        ({ data = [] }) => {
          this.page += 1
          this.formateUsers(data, more => {
            this.$refs.loadmore.afterLoadMore(more)
          })
        }
      )
    },
  },
  watch:{
    sex:function () {
      this.$refs.loadmore.beforeRefresh();
    }
  }

}
</script>
