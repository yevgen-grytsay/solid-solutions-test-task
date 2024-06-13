/* global $ */
import html from './html.js'

class TreeComponent {
    static EVENT_ADD_NODE = 'add-node'
    static EVENT_DELETE_NODE = 'delete-node'
    static EVENT_UPDATE_NODE = 'update-node'

    $container
    tree
    $treeContainer
    r
    renderer

    constructor($container) {
        this.tree = null
        this.$container = $container
        this.renderer = html.rendererSingleton()
        this.r = this.renderer.render.bind(this.renderer)
    }

    init() {
        this.$treeContainer = $('<div class="tree-container"></div>').appendTo(
            this.$container
        )

        const $bus = $(this)

        this.$container.on('node:name:change', '.node-row', e => {
            console.log({ detail: e.detail })

            $bus.trigger(TreeComponent.EVENT_UPDATE_NODE, [
                { id: e.detail.id, name: e.detail.name },
            ])
        })

        this.$container.on('click', '.item-add', e => {
            const id = $(e.target).data('node_id')

            $bus.trigger(TreeComponent.EVENT_ADD_NODE, [{ id }])
        })

        this.$container.on('click', '.item-delete', e => {
            const id = $(e.target).data('node_id')

            $bus.trigger(TreeComponent.EVENT_DELETE_NODE, [{ id }])
        })

        this.$container.on('click', '.item-edit', e => {
            const $el = $(e.target)
            $el.closest('.item')
                .find('p.text .name')
                .prop('contentEditable', 'plaintext-only')
        })

        this.$container.on('click', '.item-save', e => {
            const $el = $(e.target)
            const $item = $el.closest('.item')
            const id = $el.data('node_id')
            const newName = $item.find('p.text .name').text()

            $el.closest('.item')
                .find('p.text .name')
                .prop('contentEditable', 'false')

            $bus.trigger(TreeComponent.EVENT_UPDATE_NODE, [
                { id, name: newName },
            ])
        })
    }

    render() {
        const $treeContainer = $('<div class="tree-container"></div>')
        const $el = this.#createNodes([this.tree.root])
        $treeContainer.append($el)
        this.$treeContainer.replaceWith($treeContainer)
        this.$treeContainer = $treeContainer
        this.renderer.assign($treeContainer)
    }

    setState(tree) {
        this.tree = tree
    }

    #renderNode(child) {
        return this.r(({ assign }) => {
            return `
                <div 
                    class="item"
                    ${assign('node', child)}
                >
                    <p class="text">
                        <editable-node class="node-row" node_id="${child.id}" node_name="${child.name}"></editable-node>
                        
                    </p>
                    <div class="children">
                        ${html.map(child.children, child => {
                            return this.#renderNode(child)
                        })}
                    </div>
                </div>
            `
        })
    }

    #createNodes(nodesStateList) {
        const parts = nodesStateList.map(child => {
            return this.#renderNode(child)
        })

        return $(parts.join('\n'))
    }
}

class EditableNode extends HTMLElement {
    static observedAttributes = ['node_id', 'node_name']

    style

    constructor() {
        super()
    }
    // Element functionality written in here

    connectedCallback() {
        this.style = document.createElement('style')
        this.append(this.style)

        // this.modeReadOnly()

        const $el = $(`
            <div>
                <div>
                    <div class="name-container"><span class="name"></span></div>
                    <div class="input-container"><input name="node_name" class="form-control" type="text"></div>
                </div>
                <div class="btn-group">
                   <button type="button" class="btn btn-light btn-sm item-add">+</button>
                   <button type="button" class="btn btn-light btn-sm item-delete">-</button>
                   <button type="button" class="btn btn-light btn-sm item-edit">edit</button>
                   <button type="button" class="btn btn-light btn-sm item-cancel">cancel</button>
                   <button type="button" class="btn btn-light btn-sm item-save">save</button>
                </div>
            </div>
        `)

        this.append($el.get(0))

        $(this)
            .find('.name-container .name')
            .text(this.getAttribute('node_name'))

        $(this).on('change', '[name="node_name"]', e => {
            this.dispatchEvent(
                new CustomEvent('node:name:change', {
                    bubbles: true,
                    detail: {
                        id: this.getAttribute('node_id'),
                        name: $(e.target).val(),
                    },
                })
            )
        })

        $(this).on('click', '.item-edit', e => {
            this.modeEdit()
        })
    }

    modeEdit() {
        const style = document.createElement('style')
        style.innerHTML = `
            [node_id="${this.getAttribute('node_id')}"] .name-container { display: none; }
            [node_id="${this.getAttribute('node_id')}"] .input-container { display: block; }
        `
        this.style = style
        const el = this.querySelector('style')
        el.replaceWith(style)
    }

    modeReadOnly() {
        const style = document.createElement('style')
        style.innerHTML = `
            [node_id="${this.getAttribute('node_id')}"] .name-container { display: block; }
            [node_id="${this.getAttribute('node_id')}"] .input-container { display: none; }
        `
        this.style = style
        const el = this.querySelector('style')
        el.replaceWith(style)
    }
}

customElements.define('editable-node', EditableNode)

export default TreeComponent
