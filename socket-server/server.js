const express = require('express');
const { Server } = require("socket.io");

const app = express();
const server = require('http').createServer(app);
const io = new Server(server, {
    cors: {
        origin: '*',
    },
});

app.use(express.json());

app.post('/emit', (req, res) => {
    const { event, data } = req.body;
    io.emit(event, data);
    res.status(200).send('Evento emitido correctamente');
});

io.on('connection', (socket) => {
    console.log(`Cliente conectado: ${socket.id}`);
});

server.listen(3001, () => {
    console.log('Servidor Socket.IO escuchando en el puerto 3001');
});
