import api from './api'
import lstore from '@/plugins/lstore/lstore'

/**
 * api base url
 */
export const baseURL =
  process.env.NODE_ENV === 'production'
    ? `${process.env.VUE_APP_API_HOST}/api/${process.env.VUE_APP_API_VERSION}`
    : '/api/v2'

/**
 * 统一接口请求数据返回数量限制
 */
export const limit = ~~(lstore.getData('BOOTSTRAPPERS') || {}).limit || 15

/**
 * 上传文件
 * @author mutoe <mutoe@foxmail.com>
 * @export
 * @param {FormData} formData
 * @returns
 */
export function postFile (formData) {
  return api.post('/files', formData, { validateStatus: s => s === 201 })
}

/**
 * 举报评论
 *
 * @author mutoe <mutoe@foxmail.com>
 * @export
 * @param {number} commentId
 * @param {string} reason 举报原因
 * @returns
 */
export function reportComment (commentId, reason) {
  const url = `/report/comments/${commentId}`
  return api.post(url, { reason }, { validateStatus: s => s === 201 })
}
