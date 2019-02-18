// import AppDB from "@/vendor/easemob/AppDB.js";
import bus from "@/bus.js";
import http from "@/api/api.js";
import lstore from "@/plugins/lstore/lstore.js";

const state = {
    status: false,
    // chatRooms: new Map(),
    chatRooms: [],
    messages: new Map(),
    newMessage: false,
    routeChat:false//是否是在聊天\room列表页
};

const actions = {
    initChatRooms({commit, rootState: {CURRENTUSER}}) {
        return new Promise((resolve, reject) => {
            AppDB.init(CURRENTUSER.id)
                .GetChatRooms()
                .then(rooms => {
                    commit("initChatRooms", rooms);
                    resolve(rooms);
                })
                .catch(err => {
                    reject(err);
                });
        });
    },
    createRoom({commit}, room) {
        commit("createRoom", room);
        console.log(1);
    }
};

const mutations = {
    createRoom(state, room) {
        state.chatRooms.push(room);
        console.log(2);
    },
    updateRoom(state, info) {
        // state.chatRooms.get(room.id).lastd;
        // console.log(33);
        for (let key in state.chatRooms) {
            if (parseInt(info.id) == parseInt(state.chatRooms[key].id)) {
                state.chatRooms[key].latest = {data: info.latest};
                state.chatRooms[key].time = info.time;
                break;
            }
        }
    },
    routeChat(state,b){
        state.routeChat=b;
    },
    SOCKET_CONNECT: (state, status) => {
        state.status = true;
    },
    SOCKET_USER_MESSAGE: (state, message) => {
        // console.log("get socket message");
        // console.log(messages);
        // for (let message of messages) {
            if (state.messages.get(message.from)) {
                // console.log(state.MESSAGE.messages.get(message.from_user_id));
                state.messages.get(message.from).push(message);
            } else {
                let messageArr = [];
                messageArr.push(message)
                state.messages.set(message.from, messageArr);
            }
        // }
        // state.MESSAGE.messages.push(message);
        if (state.messages.get(message.from).length == 1) {
            bus.$emit("UpdateRoomMessages");
        }

        //是否已创建 room
        let ifHave = false;
        for (let key in state.chatRooms) {
            if (message.from == parseInt(state.chatRooms[key].id)) {
                ifHave = true;
                break;
            }
        }
        if (!ifHave) {
            http
                .get(`users/${message.from}`)
                .then((user) => {
                    let room = {
                        avatar: user.data.avatar,
                        id: user.data.id,
                        info: user.data,
                        latest: {data: message.source.data},
                        name: user.data.name,
                        time: Date.parse(new Date()),
                        type: "chat",
                        unreadCount: 0,
                        user: lstore.getData('H5_CUR_USER')
                    };
                    state.chatRooms.push(room);
                });
        } else {
            for (let key in state.chatRooms) {
                if (message.from == parseInt(state.chatRooms[key].id)) {
                    state.chatRooms[key].latest = {data: message.source.data};
                    state.chatRooms[key].time = message.time;
                    break;
                }
            }
        }
        if(!state.routeChat){
            state.newMessage = true;
        }

    },
    CLEAR_NEW_MESSAGE: (state) => {
        state.newMessage = false;
    },
    SEND_MESSAGE: (state, message) => {
// console.log(message);
        if (state.messages.get(parseInt(message.to))) {
            state.messages.get(parseInt(message.to)).push(message);
        } else {
            let messageArr = [];
            messageArr.push(message)
            state.messages.set(parseInt(message.to), messageArr);
        }
    }
};

const getters = {
    hasRoom: ({chatRooms}) => id => {
        for (let room of chatRooms) {
            if (parseInt(id) == parseInt(room.id))
                return room;
        }
        return false;
    }
}

export default {state, getters, actions, mutations};
