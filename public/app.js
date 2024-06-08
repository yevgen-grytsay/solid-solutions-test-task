import { getAllNodes, createNode } from './api.js'
import TreeComponent from './tree-component.js'


const initApp = () => {
    const $app = $('#app');
    const component = new TreeComponent($app)

    component.init()

    getAllNodes()
        .then(resp => {
            component.setState(resp.data)
            component.render()
        })


    const refresh = () => {
        getAllNodes()
            .then(resp => {
                component.setState(resp.data)
                component.render()
            })
            .catch(e => {
                console.error(e)
            })
    }

    $(component).on(TreeComponent.EVENT_ADD, (e, {id}) => {
        console.log('add event', id)

        // todo ui.block()

        createNode(id)
            .then(refresh)
    })
}

$(document).ready(initApp);
