<template>
  <div :class="prefixCls">
    <CommonHeader>社区签到排行榜</CommonHeader>

    <LoadMore
      ref="loadmore"
      :on-refresh="onRefresh"
      :on-load-more="onLoadMore"
    >
      <div :class="`${prefixCls}-list`">
        <RankListItem
          v-for="(user, index) in users"
          :key="user.id"
          :prefix-cls="prefixCls"
          :user="user"
          :index="index"
        >
          <p>累计签到：{{ user.extra.checkin_count || 0 }}</p>
        </RankListItem>
      </div>
    </LoadMore>
  </div>
</template>

<script>
import RankListItem from '../components/RankListItem.vue'
import { getRankUsers } from '@/api/ranks.js'
import { limit } from '@/api'

const api = '/checkin-ranks'
const prefixCls = 'rankItem'

export default {
  name: 'CheckinList',
  components: {
    RankListItem,
  },
  data () {
    return {
      prefixCls,
      loading: false,
      vuex: 'rankCheckin',
    }
  },

  computed: {
    users () {
      return this.$store.getters.getUsersByType(this.vuex)
    },
  },

  methods: {
    cancel () {
      this.to('/rank/users')
    },
    to (path) {
      path = typeof path === 'string' ? { path } : path
      if (path) {
        this.$router.push(path)
      }
    },
    onRefresh () {
      getRankUsers(api).then(data => {
        this.$store.commit('SAVE_RANK_DATA', { name: this.vuex, data })
        this.$refs.loadmore.topEnd(false)
      })
    },
    onLoadMore () {
      getRankUsers(api, { offset: this.users.length || 0 }).then(
        (data = []) => {
          this.$store.commit('SAVE_RANK_DATA', {
            name: this.vuex,
            data: [...this.users, ...data],
          })
          this.$refs.loadmore.bottomEnd(data.length < limit)
        }
      )
    },
  },
}
</script>

<style lang="less" src="../style.less">
</style>
