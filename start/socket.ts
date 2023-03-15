import Ws from '../app/Services/Ws'

Ws.boot()

Ws.io.on('connection', (socket) => {
  socket.emit('news', { hello: 'world' })

  socket.on('addCharacter', (data) => {
    console.log('data from client: ' + JSON.stringify(data))
  })
})
