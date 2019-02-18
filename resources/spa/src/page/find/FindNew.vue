<template>
  <JoLoadMore
    key="find-new"
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
import UserItem from '@/components/UserItem.vue'
import { findUserByType } from '@/api/user.js'
export default {
  name: 'FindPop',
  components: {
    UserItem,
  },
  data () {
    return {
      users: [],
      sex:this.$store.state.USER_SEX
    }
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
    onRefresh () {
      let parm={sex:this.sex};
      findUserByType('latests',parm).then(({ data: users } = {}) => {
        users && (this.users = users)
        this.$refs.loadmore.afterRefresh(users.length < 15)
      })
    },
    onLoadMore () {
      findUserByType('latests', {
        offset: this.users.length,
        sex:this.sex
      }).then(({ data: users }) => {
        this.users = [...this.users, ...users]
        this.$refs.loadmore.afterLoadmore(users.length < 15)
      })
    },
  },
  watch:{
    sex:function () {
      this.$refs.loadmore.beforeRefresh();
    }
  }
}
</script>
