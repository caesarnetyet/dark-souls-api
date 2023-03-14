import Ws from '../app/Services/Ws'

Ws.boot()

/**
 * Listen for incoming socket connections
 */
Ws.io.on('connection', (socket) => {
  socket.emit('news', { hello: 'world' })

  socket.on('addCharacter', (data) => {
    console.log('data from client: ' + JSON.stringify(data))
  })
})
