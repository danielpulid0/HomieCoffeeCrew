-- 1. Creación de la base de datos con soporte completo para emojis y acentos
CREATE DATABASE IF NOT EXISTS homie_coffee_crew;
USE homie_coffee_crew;

-- 2. Tabla de Usuarios
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'barista', 'admin') NOT NULL DEFAULT 'cliente',
    sellos_actuales INT NOT NULL DEFAULT 0,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 3. Tabla de Bebidas
CREATE TABLE bebidas (
    id_bebida INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    categoria ENUM('Tradicional', 'Alternativo', 'No Brew') NOT NULL,
    imagen_url VARCHAR(255) DEFAULT 'img/cafeMenu.png',
    estado_disponibilidad TINYINT(1) NOT NULL DEFAULT 1
);

-- 4. Tabla de Promociones
CREATE TABLE promociones (
    id_promocion INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(120) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATE NOT NULL,
    fecha_vigencia DATE NOT NULL,
    imagen_url VARCHAR(255) DEFAULT 'img/dosCafes.png',
    estado_activo TINYINT(1) NOT NULL DEFAULT 1
);

-- =========================================================================
-- DATOS DE PRUEBA INICIALES
-- =========================================================================

-- Usuarios de prueba (Contraseña genérica para todos: 'homie1234')
INSERT INTO usuarios (nombre, correo, password, rol, sellos_actuales) VALUES
('Administradora Homie', 'admin@homie.com', '$2y$10$plgNXyI.ytfFlbpgLeqx9O3Ak6zerFOwvpyRpS/6FQWGf9CAFMjO6', 'admin', 0),
('Barista Turno', 'barista@homie.com', '$2y$10$plgNXyI.ytfFlbpgLeqx9O3Ak6zerFOwvpyRpS/6FQWGf9CAFMjO6', 'barista', 0),
('Daniel Pulido', 'daniel@correo.com', '$2y$10$plgNXyI.ytfFlbpgLeqx9O3Ak6zerFOwvpyRpS/6FQWGf9CAFMjO6', 'cliente', 7),
('Diego Pulido', 'diego@correo.com', '$2y$10$plgNXyI.ytfFlbpgLeqx9O3Ak6zerFOwvpyRpS/6FQWGf9CAFMjO6', 'cliente', 10),
('Ana Pulido', 'ana@correo.com', '$2y$10$plgNXyI.ytfFlbpgLeqx9O3Ak6zerFOwvpyRpS/6FQWGf9CAFMjO6', 'cliente', 3);

-- 18 bebidas del menú real de Homie Coffee Crew
INSERT INTO bebidas (nombre, descripcion, precio, categoria, imagen_url, estado_disponibilidad) VALUES
-- TRADICIONAL (8 bebidas)
('Espresso doble', 'Extracción doble de grano de especialidad', 30.00, 'Tradicional', 'img/cafeMenu.png', 1),
('Americano 12 oz', '12 oz • Espresso doble diluido en agua caliente', 50.00, 'Tradicional', 'img/cafeMenu.png', 1),
('Americano en las rocas', '16 oz • Espresso doble sobre hielo', 55.00, 'Tradicional', 'img/cafeMenu.png', 1),
('Cortado', '4 oz • Espresso doble cortado con leche texturizada', 40.00, 'Tradicional', 'img/cafeMenu.png', 1),
('Flat', '6 oz • Doble carga de espresso con microespuma de leche', 55.00, 'Tradicional', 'img/cafeMenu.png', 1),
('Capuccino', '8 oz • Espresso con leche vaporizada y espuma cremosa', 60.00, 'Tradicional', 'img/cafeMenu.png', 1),
('Latte', '12 oz • Espresso doble con abundante leche vaporizada', 70.00, 'Tradicional', 'img/cafeMenu.png', 1),
('Latte en las rocas', '16 oz • Espresso doble con leche fría sobre hielo', 75.00, 'Tradicional', 'img/cafeMenu.png', 1),

-- ALTERNATIVO (3 bebidas)
('Espresso tónico', '16 oz • Agua tónica con espresso y notas cítricas (Limón / Naranja)', 100.00, 'Alternativo', 'img/cafeMenu.png', 1),
('Matcha tónico', '16 oz • Matcha ceremonial batido sobre agua tónica', 105.00, 'Alternativo', 'img/cafeMenu.png', 1),
('Coco kush', '16 oz • Agua de coco natural c/ matcha foam', 110.00, 'Alternativo', 'img/cafeMenu.png', 1),

-- NO BREW (7 bebidas)
('Chai caliente', '12 oz • Infusión especiada de té chai con leche vaporizada', 70.00, 'No Brew', 'img/cafeMenu.png', 1),
('Chai en las rocas', '16 oz • Infusión especiada de té chai fría con leche', 80.00, 'No Brew', 'img/cafeMenu.png', 1),
('Matcha latte caliente', '12 oz • Matcha ceremonial con leche vaporizada', 80.00, 'No Brew', 'img/cafeMenu.png', 1),
('Matcha latte en las rocas', '16 oz • Matcha ceremonial frío con leche sobre hielo', 90.00, 'No Brew', 'img/cafeMenu.png', 1),
('Tisana 12 oz', '12 oz • Infusión frutal y herbal caliente sin cafeína', 80.00, 'No Brew', 'img/cafeMenu.png', 1),
('Tisana 16 oz', '16 oz • Infusión frutal y herbal refrescante sin cafeína', 90.00, 'No Brew', 'img/cafeMenu.png', 1),
('Limonada de arándano', '16 oz • Endulzada naturalmente con miel de abeja', 85.00, 'No Brew', 'img/cafeMenu.png', 1);

-- Promociones iniciales
INSERT INTO promociones (titulo, descripcion, fecha_inicio, fecha_vigencia, imagen_url, estado_activo) VALUES
('Especial de Temporada', 'Pregunta por nuestra bebida especial del mes en barra.', '2026-10-01', '2026-12-31', 'img/dosCafes.png', 1),
('Coockie&Coffee', 'Compra un café y llévate una galleta con 25% de descuento.', '2026-10-01', '2026-12-31', 'img/dosCafes.png', 1),
('Cinammon lattes al 3x2', 'Válido de martes a jueves en bebidas de 16 oz.', '2026-10-01', '2026-12-31', 'img/dosCafes.png', 1);
