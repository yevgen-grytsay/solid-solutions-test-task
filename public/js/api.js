const _getAllNodes = () => {
    return fetch('/get-all')
        .then(resp => {
            return resp.json()
        })
        .catch(e => {
            return new Error(`Can not get nodes: ${e.message}`)
        })
}

const _getAllNodesError = () => {
    return new Promise((resolve, reject) => {
        const handlerList = [
            () => {
                reject(new Error('Unknown error'))
            },
            () => {
                resolve({
                    success: false,
                    error: {
                        code: 115,
                        message: 'Server not ready',
                    },
                })
            },
        ]
        const index = Math.floor(Math.random() * handlerList.length)

        setTimeout(handlerList[index], 1000)
    })
}

const simulateError = false
export const getAllNodes = simulateError ? _getAllNodesError : _getAllNodes

export const createNode = parentId => {
    return fetch(`/create?parent_id=${parentId}`, {
        method: 'POST',
    })
}

/**
 * @param {Number} id
 * @return Promise
 */
export const deleteNode = id => {
    return fetch(`/delete?id=${id}`, {
        method: 'POST',
    })
}

export const updateNode = (id, data) => {
    return fetch(`/update?id=${id}`, {
        method: 'POST',
        body: JSON.stringify(data),
        headers: {
            'Content-Type': 'application/json',
        },
    })
}
