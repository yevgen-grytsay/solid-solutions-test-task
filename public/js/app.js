/* global $, bootstrap */
import {createNode, deleteNode, getAllNodes} from './api.js'
import TreeComponent from './tree-component.js'


function unblockUi() {
    $('#app').show();
}
function blockUi() {
    $('#app').hide();
}

/**
 * @param {Error|null} e
 */
function notifyError(e) {
    const toastEl = document.getElementById('appToast')
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastEl)

    const message = typeof e === 'undefined'
        ? 'Unknown error'
        : e.message;
    $(toastEl)
        .find('.toast-body')
        .text(message)

    toastBootstrap.show()
}

const initApp = () => {
    const component = new TreeComponent($('#app'))

    component.init()

    const onfulfilled = resp => {
        component.setState(resp.data.tree)
        component.render()
    };

    const filterApiResponse = (resp) => {
        if (false === resp.success) {
            throw new Error(resp.error.message)
        }

        return resp
    }

    const refresh = () => {
        return getAllNodes()
            .then(filterApiResponse)
            .then(onfulfilled)
            .catch(notifyError)
    }

    refresh()

    $(component).on(TreeComponent.EVENT_ADD_NODE, (e, {id}) => {
        blockUi()
        createNode(id)
            .then(refresh)
            .finally(unblockUi)
    })

    $(component).on(TreeComponent.EVENT_DELETE_NODE, (e, {id}) => {
        blockUi()
        deleteNode(id)
            .then(refresh)
            .finally(unblockUi)
    })
}

$(document).ready(initApp);
