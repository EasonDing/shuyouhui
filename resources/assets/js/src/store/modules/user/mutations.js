import {
    SET_CURRENT_USER
} from './../../mutation-types';

export default {
    [SET_CURRENT_USER](state, user) {
        state.user = user;
    }
};
