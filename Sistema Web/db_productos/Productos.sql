
CREATE TABLE marcas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE
);


CREATE TABLE productos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    marca_id INTEGER NOT NULL,
    producto VARCHAR(100) NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (marca_id) REFERENCES marcas(id)
);


-- Insertar las marcas
INSERT INTO marcas (nombre) VALUES
('COCA COLA'),
('BIMBO'),
('MARINELA'),
('SABRITAS'),
('LA COSTEÑA');



-- COCA COLA 
INSERT INTO productos (marca_id, producto, precio) VALUES
(1, 'Coca Cola 600 ml', 22.00),
(1, 'Coca Cola 2L', 39.50),
(1, 'Fuze Tea 600 ml', 19.00),
(1, 'Powerade', 31.00);

-- BIMBO 
INSERT INTO productos (marca_id, producto, precio) VALUES
(2, 'Pan Blanco Grd', 50.00),
(2, 'Pan Blanco ch', 26.00),
(2, 'Pan Integral Grd', 56.00),
(2, 'Pan Integral ch', 28.00),
(2, 'Pan Tostado', 50.00);

-- MARINELA 
INSERT INTO productos (marca_id, producto, precio) VALUES
(3, 'Canelitas', 24.00),
(3, 'Polvorones', 22.00),
(3, 'Barritas', 18.00),
(3, 'Rebanadas', 10.00),
(3, 'Principe', 24.00);

-- SABRITAS
INSERT INTO productos (marca_id, producto, precio) VALUES
(4, 'Cheetos', 18.00),
(4, 'Doritos rojos', 22.00),
(4, 'Doritos Diablo', 22.00),
(4, 'Fritos', 18.00),
(4, 'Sabritas Crujientes', 22.00),
(4, 'Sabritas Especias', 22.00);

-- LA COSTEÑA 
INSERT INTO productos (marca_id, producto, precio) VALUES
(5, 'Elotes', 13.00),
(5, 'Zanahorias', 18.00),
(5, 'Chile Chipotle', 19.00),
(5, 'Rajas', 12.00);






