CREATE TABLE Cliente (
    clienteID INT PRIMARY KEY AUTO_INCREMENT,
    nombreCliente VARCHAR(50) NOT NULL,
    apellidoCliente VARCHAR(50) NOT NULL,
    telefonoCliente VARCHAR(20),
    cedulaCliente VARCHAR(20) UNIQUE NOT NULL
);