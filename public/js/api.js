let autoIncrement = 5;
let tree = {
    root: {
        id: 1,
        name: "Content Root method=GET",
        children: [
            {
                id: 2,
                name: "Item 1-1",
                children: [
                    {
                        id: 4,
                        name: "Item 1-1-1",
                        children: []
                    },
                    {
                        id: 5,
                        name: "Item 1-1-2",
                        children: []
                    },
                ]
            },
            {
                id: 3,
                name: "Item 1-2",
                children: []
            },
        ],
    }
};

/*function mapTree(node, callback) {
    const newNode = callback(node);
    if (newNode === null) {
        return null;
    }

    return {
        ...newNode,
        children: node.children
            .map(child => mapTree(child, callback))
            .filter(item => item !== null)
    }
}*/

function mapTree2(nodeList, callback) {

    return nodeList
        .map(node => {
            const newNode = callback(node);
            if (newNode === null) {
                return null;
            }

            return {
                ...newNode,
                children: mapTree2(node.children, callback)
            }
        })
        .filter(item => item !== null)
}

export const getAllNodes = () => {
    return Promise.resolve({
        success: true,
        data: tree
    })

    /*return fetch("/get-all")
        .then(resp => {
            return resp.json()
        })
        .catch(e => {
            return new Error(`Can not get nodes: ${e.message}`)
        })*/
}

export const createNode = (parentId) => {
    autoIncrement++
    const newId = autoIncrement;

    const callback = node => {
        if (node.id === parentId) {
            node.children.push({
                id: newId,
                name: `Item ${newId}`,
                children: []
            })
        }

        return node
    };

    const nodeList = mapTree2([tree.root], callback)

    tree = {
        ...tree,
        root: nodeList[0],
    }

    return Promise.resolve()
    /*fetch(`/create?parent_id=${parentId}`, {
        method: 'POST'
    })*/
}

/**
 * @param {Number} id
 * @return Promise
 */
export const deleteNode = (id) => {
    const callback = node => {
        if (node.id === id) {
            return null
        }

        return node
    };


    const nodeList = mapTree2([tree.root], callback)
    if (nodeList.length === 0) {
        return Promise.resolve({
            success: false,
            error: {
                message: 'Can not delete root node',
            }
        })
    }

    tree = {
        ...tree,
        root: nodeList[0],
    }

    return Promise.resolve()

    /*fetch(`/delete?id=${id}`, {
        method: 'POST',
    })*/
}

export default {
    getAllNodes,
    createNode,
    deleteNode,
};
