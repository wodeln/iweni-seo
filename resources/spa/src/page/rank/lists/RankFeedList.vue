<template>
  <div :class="prefixCls">
    <CommonHeader>{{ title }}动态排行榜</CommonHeader>

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
          <p>点赞量：{{ user.extra.count || 0 }}</p>
        </RankListItem>
      </div>
    </LoadMore>
  </div>
</template>

<script>
import RankListItem from '../components/RankListItem.vue'
import { getRankUsers } from '@/api/ranks.js'
import { limit } from '@/api'

const prefixCls = 'rankItem'
const api = '/feeds/ranks'
const config = {
  week: {
    vuex: 'rankFeedsWeek',
    title: '本周',
    query: 'week',
  },
  today: {
    vuex: 'rankFeedsToday',
    title: '今日',
    query: 'day',
  },
  month: {
    vuex: 'rankFeedsMonth',
    title: '本月',
    query: 'month',
  },
}

export default {
  name: 'FeedsList',
  components: {
    RankListItem,
  },
  data () {
    return {
      prefixCls,
      loading: false,
      title: '', // 标题
      vuex: '', // vuex主键
      query: '', // api查询query
    }
  },

  computed: {
    users () {
      return this.$store.getters.getUsersByType(this.vuex)
    },
  },
  created () {
    let time = this.$route.params.time || 'today'
    this.title = config[time].title
    this.vuex = config[time].vuex
    this.query = config[time].query
    if (this.users.length === 0) {
      this.onRefresh()
    }
  },

  methods: {
    cancel () {
      this.to('/rank/feeds')
    },
    to (path) {
      path = typeof path === 'string' ? { path } : path
      if (path) {
        this.$router.push(path)
      }
    },
    onRefresh () {
      getRankUsers(api, { type: this.query }).then(data => {
        this.$store.commit('SAVE_RANK_DATA', { name: this.vuex, data })
        this.$refs.loadmore.topEnd(false)
      })
    },
    onLoadMore () {
      getRankUsers(api, {
        type: this.query,
        offset: this.users.length || 0,
      }).then((data = []) => {
        this.$store.commit('SAVE_RANK_DATA', {
          name: this.vuex,
          data: [...this.users, ...data],
        })
        this.$refs.loadmore.bottomEnd(data.length < limit)
      })
    },
  },
}
</script>

<style lang="less" src="../style.less">
</style>
