/* global $, bootstrap */
import { createNode, deleteNode, updateNode, getAllNodes } from './api.js'
import TreeComponent from './tree-component.js'

function unblockUi() {
    $('#app').show()
}
function blockUi() {
    $('#app').hide()
}

/**
 * @param {Error|null} e
 */
function notifyError(e) {
    const toastEl = document.getElementById('appToast')
    const toastBootstrap = bootstrap.Toast.getOrCreateInstance(toastEl)

    const message = typeof e === 'undefined' ? 'Unknown error' : e.message
    $(toastEl).find('.toast-body').text(message)

    toastBootstrap.show()
}

const initApp = () => {
    const component = new TreeComponent($('#app'))

    component.init()

    const onfulfilled = resp => {
        component.setState(resp.data.tree)
        component.render()
    }

    const filterApiResponse = resp => {
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

    $(component).on(TreeComponent.EVENT_ADD_NODE, (e, { id }) => {
        blockUi()
        createNode(id).then(refresh).finally(unblockUi)
    })

    $(component).on(TreeComponent.EVENT_DELETE_NODE, (e, { id }) => {
        blockUi()
        deleteNode(id).then(refresh).finally(unblockUi)
    })

    $(component).on(TreeComponent.EVENT_UPDATE_NODE, (e, { id, name }) => {
        blockUi()
        updateNode(id, { name }).then(refresh).finally(unblockUi)
    })
}

$(document).ready(initApp)

$($ => {
    const data = {
        root: {
            id: 1,
            name: 'root node',
            children: [
                {
                    id: 2,
                    name: 'node #2',
                    children: [],
                },
                {
                    id: 3,
                    name: 'node #3',
                    children: [
                        {
                            id: 4,
                            name: 'node #4',
                            children: [],
                        },
                        {
                            id: 5,
                            name: 'node #5',
                            children: [],
                        },
                    ],
                },
            ],
        },
    }

    const h = {
        $if(valueOrFunction, a, b) {},
    }

    const template = `
        <div>
            <p data-$if="${ctx => (ctx.name.length > 0 ? ':name="ctx.name"' : '')}"></p>
        </div>
    `

    function render(template, ctx) {}

    const ctx = data.root
    render(template, ctx)

    console.log(template)
})
