<template>
  <div class="p-profile-collection-feeds">
    <JoLoadMore
      ref="loadmore"
      :auto-load="false"
      style="padding-top: .9rem"
      @onRefresh="onRefresh"
      @onLoadMore="onLoadMore"
    >
      <ul>
        <li
          v-for="feed in feedList"
          :key="`clet-${feed.id}`"
          class="p-profile-collection-feeds-item"
        >
          <FeedCard
            :feed="feed"
            :show-footer="false"
          />
        </li>
      </ul>
    </JoLoadMore>
  </div>
</template>

<script>
import FeedCard from '@/components/FeedCard/FeedCard.vue'
import { limit } from '@/api'
import * as api from '@/api/feeds'

export default {
  name: 'ProfileCollectionFeeds',
  components: {
    FeedCard,
  },
  data () {
    return {
      feedList: [],
    }
  },
  mounted () {
    this.$refs.loadmore.beforeRefresh()
  },
  methods: {
    onRefresh () {
      // TODO: refactor there with vuex action.
      api.getCollectedFeed().then(({ data = [] }) => {
        this.feedList = data
        this.$refs.loadmore.afterRefresh(data.length < limit)
      })
    },
    onLoadMore () {
      const offset = this.feedList.length
      // TODO: refactor there with vuex action.
      api.getCollectedFeed({ offset }).then(({ data = [] }) => {
        this.feedList = [...this.feedList, ...data]
        this.$refs.loadmore.afterLoadMore(data.length < limit)
      })
    },
  },
}
</script>

<style lang="less" scoped>
.p-profile-collection-feeds {
  &-item {
    background-color: #fff;
    padding-bottom: 20px;

    .m-card-main {
      padding-bottom: 30px;
    }
  }

  &-item + &-item {
    margin-top: 10px;
  }
}
</style>
