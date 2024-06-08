/* global $ */
import { getAllNodes, createNode, deleteNode } from './api.js'
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

    $(component).on(TreeComponent.EVENT_ADD_NODE, (e, {id}) => {
        // todo ui.block()
        createNode(id)
            .then(refresh)
    })

    $(component).on(TreeComponent.EVENT_DELETE_NODE, (e, {id}) => {
        // todo ui.block()
        deleteNode(id)
            .then(refresh)
    })
}

$(document).ready(initApp);