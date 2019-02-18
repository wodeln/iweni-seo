<template>
  <JoLoadMore
    key="find-pop"
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
    onRefresh (callback) {
        let parm={sex:this.sex};
        // console.log(this.$route.params.sex);
      findUserByType('populars',parm).then(({ data: users } = {}) => {
        users && (this.users = users)
        this.$refs.loadmore.afterRefresh(users.length < 15)
      })
    },
    onLoadMore (callback) {
      findUserByType('populars', {
        offset: this.users.length,
        sex: this.sex
      }).then(({ data: users }) => {
        this.users = [...this.users, ...users]
        this.$refs.loadmore.afterLoadMore(users.length < 15)
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
