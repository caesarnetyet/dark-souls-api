import Ws from '../app/Services/Ws'

Ws.boot()

Ws.io.on('connection', (socket) => {
  console.log('connected')
  socket.on('requestUpdate', (data) => {
    console.log('data from client: ' + JSON.stringify(data))
    Ws.io.emit('updateCharacters', "from socket.ts:  ")
  })
})
