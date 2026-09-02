-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-08-2026 a las 21:07:01
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_voto`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id_alumno` int(11) NOT NULL,
  `dni` varchar(15) NOT NULL,
  `curso` int(11) NOT NULL,
  `division` int(11) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id_alumno`, `dni`, `curso`, `division`, `apellido`, `nombre`) VALUES
(1, '53341410', 1, 1, 'ACEVEDO', 'Dubini Kevin'),
(2, '53338829', 1, 1, 'AGUIRRE IBARRA', 'Bastian Emanuel'),
(3, '53764048', 1, 1, 'ALEGRE', 'Ciro'),
(4, '53338589', 1, 1, 'AMARILLA TORRES', 'Emma Victoria'),
(5, '53549466', 1, 1, 'BENCHARSKI', 'Leonel Mateo'),
(6, '53550794', 1, 1, 'CABRERA', 'Fabrizio'),
(7, '53340890', 1, 1, 'CARDOZO', 'Morena Sofia'),
(8, '53869271', 1, 1, 'ESPINOSA', 'Tatiana Nerea'),
(9, '53550768', 1, 1, 'GIMÉNEZ', 'Victoria Soledad'),
(10, '53871769', 1, 1, 'GOMEZ', 'Paloma Nicole Noraly'),
(11, '54079957', 1, 1, 'GOMEZ', 'Tiana Magali'),
(12, '53548439', 1, 1, 'KOYARKI', 'Mateo Agustín'),
(13, '53548597', 1, 1, 'LENCINA', 'Denisse Florentina'),
(14, '54078977', 1, 1, 'LEONCZYK', 'Avril Gianella'),
(15, '53547296', 1, 1, 'MARTINEZ ESPINDOLA', 'Nayra Aylen'),
(16, '53868817', 1, 1, 'MEZA CABALLERO', 'Mia Abril'),
(17, '53549491', 1, 1, 'MOLINA', 'Tadeo Adrián'),
(18, '53872234', 1, 1, 'MONTAÑEZ KARLEN', 'Matias Ignacio'),
(19, '53766524', 1, 1, 'NUÑEZ', 'Uriel'),
(20, '53869184', 1, 1, 'ORUE', 'Marcelo Valentin'),
(21, '53546716', 1, 1, 'PEREZ SALAZAR', 'Valentino'),
(22, '53337027', 1, 1, 'RAMIREZ TORRES', 'Cristobal Ignacio'),
(23, '52677105', 1, 1, 'RIVERO', 'Pascuala Nahir'),
(24, '53768253', 1, 1, 'ROLDAN GOMEZ', 'Diego Ismael'),
(25, '53341467', 1, 1, 'ROMERO GOLE', 'Juana'),
(26, '53871192', 1, 1, 'ROMERO', 'Juan Ignacio Damian'),
(27, '53766047', 1, 1, 'RUIDIAZ AGÜERO', 'Bastián Nicolás'),
(28, '53341131', 1, 1, 'SAUCEDO COCCO', 'Nicole Abril'),
(29, '53547795', 1, 1, 'SCHEFER ALFONZO', 'Catherine Beatriz'),
(30, '53872422', 1, 1, 'SOTOMAYOR PENZO', 'Maximo Agustin'),
(31, '53911496', 1, 1, 'VILLALBA', 'Agustin Emiliano Walter'),
(32, '51704287', 1, 2, 'ALMENAR DE DEUS', 'Juan Cruz'),
(33, '53768678', 1, 2, 'BARRIOS', 'Fernando Michael'),
(34, '53768564', 1, 2, 'BENITEZ', 'Victor Joaquin'),
(35, '53550698', 1, 2, 'CAMERA ORTIZ', 'Santino Carlos'),
(36, '53550724', 1, 2, 'ESCALANTE SOSA', 'Danilo Valentino De Jesus'),
(37, '53548562', 1, 2, 'GARCÍA VILLALBA', 'Felipe'),
(38, '53550633', 1, 2, 'GODOY', 'Maria Joseline'),
(39, '53550957', 1, 2, 'LAGOMARCINO GARCÍA MARTÍN', 'Ariana Pilar'),
(40, '53765690', 1, 2, 'LOPEZ OVIEDO', 'Joel Miguel Emanuel'),
(41, '53546745', 1, 2, 'MARTINEZ FERNANDEZ', 'Angel Bautista'),
(42, '54079605', 1, 2, 'MEZA FAMOSO', 'Cristopher Francisco'),
(43, '53547098', 1, 2, 'OJEDA', 'Maximo Ezequiel'),
(44, '53341471', 1, 2, 'OLIVARES', 'Julio Gabriel'),
(45, '53869499', 1, 2, 'PEREYRA', 'Aquiles Gabriel'),
(46, '53262027', 1, 2, 'PEREZ CAMPOS', 'Ariadna Melina'),
(47, '53870907', 1, 2, 'QUEVEDO', 'Santino Rafael'),
(48, '53339755', 1, 2, 'RAMIREZ', 'Luciana Aylen'),
(49, '53764248', 1, 2, 'ROMERO BAEZ', 'Williams Leonardo'),
(50, '53869395', 1, 2, 'ROMERO', 'Sol Morena'),
(51, '53682622', 1, 2, 'SANCHEZ', 'Sofia Belen'),
(52, '54078527', 1, 2, 'SILVA', 'Mateo Maximiliano'),
(53, '53547031', 1, 2, 'THOUZEAU', 'Tomas Emanuel'),
(54, '53337422', 1, 2, 'VIGANO CORONEL', 'Guillermina'),
(55, '53494758', 1, 2, 'VILLALVA SOTO', 'Alejandro Mauricio'),
(56, '53550672', 1, 3, 'AGUIRRE PEREZ', 'Shamir Nicolas'),
(57, '53765639', 1, 3, 'ALMIRON', 'Alihuen Pia'),
(58, '53546055', 1, 3, 'ALTAMIRANO', 'Benjamin Leonardo'),
(59, '53547766', 1, 3, 'AQUINO', 'Valentino Xian'),
(60, '54078798', 1, 3, 'BASUALDO PIASTERLINI', 'Nayla Natali'),
(61, '53337458', 1, 3, 'BLANCO', 'Corina Magali'),
(62, '53337864', 1, 3, 'CANO', 'Sofia Victoria'),
(63, '53547708', 1, 3, 'CENTURION', 'Bastian Benjamin'),
(64, '53766423', 1, 3, 'DUSICKA', 'Lautaro Emanuel'),
(65, '53872943', 1, 3, 'ESPINOZA', 'Mauricio Saul'),
(66, '53339476', 1, 3, 'FRANCO ROYG', 'Benjamin'),
(67, '49452528', 1, 3, 'GAUNA', 'Maria Ernestina'),
(68, '53765380', 1, 3, 'GOMEZ ZALAZAR', 'Analis Avril'),
(69, '54079040', 1, 3, 'LAGRAÑA LEDEZMA', 'Santino'),
(70, '53869451', 1, 3, 'LOPEZ', 'Franco Octavio Ciro'),
(71, '53768788', 1, 3, 'MAYER', 'Alejandro Ariel'),
(72, '53765673', 1, 3, 'MEDINA', 'Francesca Fiorela'),
(73, '53868018', 1, 3, 'MONZON', 'Bruno Daniel'),
(74, '54079104', 1, 3, 'ORREGO', 'Oriana Malena'),
(75, '54078764', 1, 3, 'PERESSOTTI', 'Tahiel Dalan Nicolas'),
(76, '53547711', 1, 3, 'QUINTERO', 'Milena'),
(77, '53337036', 1, 3, 'REINOSO MACIEL', 'Lionel Brian'),
(78, '53337436', 1, 3, 'RODRIGUEZ MORALES', 'Ignacio Gael'),
(79, '53871727', 1, 3, 'ROMERO FIGUERERO', 'Ana Sol'),
(80, '53549450', 1, 3, 'ROMERO', 'Milena Dulce Itati'),
(81, '53548563', 1, 3, 'SANCHEZ FERNANDEZ', 'Antonella'),
(82, '52098641', 1, 3, 'SANDOVAL', 'Jazmin Brisa Abigail'),
(83, '53165468', 1, 3, 'SOLER', 'Benjamín Angel Emanuel'),
(84, '53767423', 1, 3, 'SOTO', 'Ian Lazaro'),
(85, '53401965', 1, 3, 'TORRES', 'Luzmila Maria Celeste'),
(86, '53870778', 1, 3, 'VALLEJOS FLEITAS', 'Santino Jonas'),
(87, '53768146', 1, 4, 'ALFONZO ZIBELMAN', 'Mayte Alma'),
(88, '54078408', 1, 4, 'BARRIENTOS GARCIA', 'Benicio Daniel Antonio'),
(89, '53549369', 1, 4, 'BARTOLO', 'Luana Nicole'),
(90, '53340871', 1, 4, 'BLANCO', 'Mia Geraldine'),
(91, '53872269', 1, 4, 'CABRERA DIAZ', 'Teo Nicolas'),
(92, '54079053', 1, 4, 'CANTERO', 'Geronimo Bautista'),
(93, '53546296', 1, 4, 'CAÑETE RAUCH', 'Juliana Daniela'),
(94, '53546014', 1, 4, 'DALPOZZOLO', 'Emma'),
(95, '53338939', 1, 4, 'DUBARRY LOPEZ', 'Kenya Evangelina'),
(96, '53550514', 1, 4, 'ECHEVARRÍA', 'Umma Camila'),
(97, '53872218', 1, 4, 'ESPINOZA', 'Alma Alejandra'),
(98, '54078505', 1, 4, 'ESTRADA RAIMAPO', 'Nehuen Ezequiel'),
(99, '53549641', 1, 4, 'FERNANDEZ', 'Ivan Carlos Jesus'),
(100, '53766082', 1, 4, 'FRUTOS', 'Benjamin Emmanuel'),
(101, '53546021', 1, 4, 'GAUNA', 'Zoe Aylen'),
(102, '53549183', 1, 4, 'LEIVA', 'Milca Aylen'),
(103, '53766743', 1, 4, 'LYTWYN', 'Lucila Del Mar'),
(104, '54079167', 1, 4, 'MALDONADO', 'Pablo David'),
(105, '53766018', 1, 4, 'MORINIGO SILVERO', 'Ambar Martina'),
(106, '54079710', 1, 4, 'PEREYRA FAYARO', 'Tomas Nestor'),
(107, '53340866', 1, 4, 'RAMÍREZ BARRIOS', 'Maia Aimará'),
(108, '53871197', 1, 4, 'RIOS VALLEJOS', 'Maximo Nicolas'),
(109, '53764060', 1, 4, 'ROJAS', 'Antonio Mateo'),
(110, '54078569', 1, 4, 'ROMERO LUQUE', 'Bautista'),
(111, '53869484', 1, 4, 'SAUCEDO FERNANDEZ', 'Mateo'),
(112, '53550678', 1, 4, 'SOSA', 'Sheila Luisana'),
(113, '53868074', 1, 4, 'VELAZQUEZ', 'Leandro Hiram'),
(114, '53339778', 1, 4, 'ZOLOAGA', 'Ethan Adrian'),
(115, '52677346', 2, 1, 'ALFONZO', 'Iker Santino'),
(116, '52678935', 2, 1, 'AYALA BOTELLO', 'Ayrton Dario'),
(117, '52463838', 2, 1, 'AYALA', 'Gonzalo Francisco'),
(118, '52098160', 2, 1, 'BARRIOS', 'Iker De Jesus'),
(119, '52884514', 2, 1, 'BENITEZ MARTINEZ', 'Valentino Ivar'),
(120, '52678697', 2, 1, 'BENÍTEZ SOSA', 'Yamila Irupe'),
(121, '52899650', 2, 1, 'BRITEZ', 'Thiago Benjamin'),
(122, '52677230', 2, 1, 'CORREA', 'Fabricio Nicolas'),
(123, '52737831', 2, 1, 'ESCOBAR', 'Irupe Jazmin'),
(124, '52481966', 2, 1, 'GALARZA ENRIQUE', 'Maximo'),
(125, '50621148', 2, 1, 'GAMARRA', 'Martina Isabella'),
(126, '53010081', 2, 1, 'GARCIA BIANCHI', 'Joaquin'),
(127, '52738444', 2, 1, 'GAUNA', 'Bianca Trinidad'),
(128, '52679400', 2, 1, 'GOMEZ', 'Thiago Damian'),
(129, '52098816', 2, 1, 'GOMEZ', 'Tobias Leonel'),
(130, '52736075', 2, 1, 'GONZALEZ ORTIZ', 'Maia Oriana'),
(131, '52882005', 2, 1, 'GRILLO', 'Tomas'),
(132, '53010621', 2, 1, 'IBALO', 'Maximo Agustin'),
(133, '52737029', 2, 1, 'IBARRA SCHNEEBERGER', 'Lorenzo Emiliano'),
(134, '53163854', 2, 1, 'LUNA BARRIOS', 'Luana Aylin'),
(135, '52884426', 2, 1, 'MAIDANA', 'Lionel Alessandro'),
(136, '53163865', 2, 1, 'MALVAREZ', 'Martina'),
(137, '53010753', 2, 1, 'MERELE CABRERA', 'Alexander Emanuel'),
(138, '53012214', 2, 1, 'NUÑEZ', 'Nahila Nicole'),
(139, '53166479', 2, 1, 'OJEDA', 'Martina Nicole Antonia'),
(140, '52884438', 2, 1, 'PEREZ', 'Melany Ariana'),
(141, '52479933', 2, 1, 'QUIROZ', 'Malani Eliana'),
(142, '53013219', 2, 1, 'RODRIGUEZ', 'Alexander Ezequiel'),
(143, '53013348', 2, 1, 'SOSA', 'Genaro'),
(144, '52481620', 2, 1, 'TRANGONI', 'Amelia Catalina'),
(145, '52738521', 2, 1, 'VALLEJOS', 'Joel Alexis'),
(146, '53012961', 2, 1, 'VELAZQUEZ', 'Luciana'),
(147, '53165505', 2, 1, 'ZARZA', 'Lautaro Bautista'),
(148, '53012733', 2, 2, 'ACEVAL', 'Benjamín'),
(149, '52678588', 2, 2, 'ALEGRE ROMERO', 'Bayron Willian'),
(150, '52082107', 2, 2, 'AYALA ESCALANTE', 'Kiara Selene'),
(151, '52679396', 2, 2, 'BILLORDO', 'Matias'),
(152, '53165906', 2, 2, 'CANTERO', 'Erika Jazmín'),
(153, '53163719', 2, 2, 'CAÑETE PERALTA', 'Bruno'),
(154, '52481639', 2, 2, 'CARBALLO RODRIGUEZ', 'Bruno'),
(155, '53165417', 2, 2, 'CONTI NIELLA', 'Lautaro'),
(156, '52882059', 2, 2, 'CUENCA GIRAZOLES', 'Octavio Rodrigo'),
(157, '52678470', 2, 2, 'DRI', 'Bautista Adrian Valentin'),
(158, '53166205', 2, 2, 'ESTEPA', 'Leonardo Dario'),
(159, '53166519', 2, 2, 'FERNANDEZ GONZALEZ', 'Alexia Janeth'),
(160, '52736855', 2, 2, 'FERNANDEZ', 'Isaias Miguel'),
(161, '52883347', 2, 2, 'FLORES', 'Juan Pablo'),
(162, '52481944', 2, 2, 'GIMENEZ GAUNA', 'Fabrizio Lihuen'),
(163, '52238041', 2, 2, 'LEZCANO MIÑO', 'Leonidas Exequiel'),
(164, '53163597', 2, 2, 'LOPEZ', 'Maia Melody'),
(165, '52737053', 2, 2, 'MENDOZA', 'Aylin Del Mar'),
(166, '53163273', 2, 2, 'MIRANDA ARCE', 'Mauricio Gabriel'),
(167, '53163794', 2, 2, 'MONTIEL', 'Zamira Maria Itati'),
(168, '52678493', 2, 2, 'MONTIVERO VELAZQUEZ', 'Luciano Yamil'),
(169, '53013730', 2, 2, 'OJEDA MACIEL', 'Nerea Valentina'),
(170, '53012737', 2, 2, 'PEREZ ACOSTA', 'Chloe Luna Liliana'),
(171, '53013771', 2, 2, 'RAMIREZ VERA', 'Briana Belen'),
(172, '53010000', 2, 2, 'RAMIREZ', 'Santino Benjamin'),
(173, '52238276', 2, 2, 'ROA', 'Luciano Valentino'),
(174, '52737972', 2, 2, 'ROMERO PONCE', 'Patricio Raul'),
(175, '52481924', 2, 2, 'ROMERO', 'Alexander Isaias'),
(176, '52239566', 2, 2, 'ROMERO', 'Priscila Ailin'),
(177, '52678619', 2, 2, 'SILVA', 'Thiago Ivan'),
(178, '53013707', 2, 2, 'SOVERON', 'Maria Paz'),
(179, '53012738', 2, 2, 'VARGAS', 'Tomas Agustin'),
(180, '52678466', 2, 2, 'VILLALBA ROMERO', 'Yoselin Brenda'),
(181, '53162239', 2, 2, 'ZAMUDIO ALBARENGA', 'Benicio Guillermo'),
(182, '53009009', 2, 2, 'ZIBELMAN', 'Olga Mia Judit'),
(183, '52738577', 2, 2, 'ZURITA CABRAL', 'Milena Valentina'),
(184, '52238155', 2, 3, 'ALARCON BRITEZ', 'Sofia Antonella Abigail'),
(185, '52974513', 2, 3, 'ALFONSO SANCHEZ', 'Jocelyn Keren'),
(186, '53010799', 2, 3, 'ALMIRON', 'Felipe Samuel'),
(187, '52679334', 2, 3, 'ALVAREZ', 'Maia Victoria'),
(188, '52238183', 2, 3, 'AQUINO', 'Luisana Aymara'),
(189, '50062024', 2, 3, 'ARAUJO', 'Valentina María Luz'),
(190, '53011935', 2, 3, 'AYALA SOTO', 'Ian Javier'),
(191, '53163138', 2, 3, 'BENEGA LUGO', 'Matias Alexander'),
(192, '53163824', 2, 3, 'CANTERO', 'Thiago Fabian'),
(193, '52737041', 2, 3, 'CARDOZO', 'Benjamin Uriel'),
(194, '52882023', 2, 3, 'CORONEL', 'Lourdes Camila'),
(195, '53163717', 2, 3, 'DIAZ TOFFALETTI', 'Luisa Noemi'),
(196, '52679772', 2, 3, 'DOMINGUEZ', 'Nahiara Stefania'),
(197, '52883612', 2, 3, 'ESCOBAR', 'Maria Paz'),
(198, '53013778', 2, 3, 'GAUNA', 'Iker Uriel'),
(199, '52408413', 2, 3, 'GOMEZ DELGADO', 'Micaela Soledad'),
(200, '53175601', 2, 3, 'GÓMEZ', 'Guillermo Isaac Alcibiades'),
(201, '52738565', 2, 3, 'GOMEZ', 'Ignacio Emanuel'),
(202, '53163879', 2, 3, 'LESIW BERMUDEZ', 'Esteban Francisco'),
(203, '52677415', 2, 3, 'MENDEZ SEGOVIA', 'Lorenzo'),
(204, '53010095', 2, 3, 'ORTIZ KRAPOVICKAS', 'Ciro'),
(205, '52679360', 2, 3, 'PARE AGUIRRE', 'Ciro Agustin'),
(206, '52882996', 2, 3, 'PORTILLO', 'Brian Valentin'),
(207, '52736744', 2, 3, 'RAMIREZ ROMERO', 'Angel Benjamin'),
(208, '52677234', 2, 3, 'RAMIREZ', 'Yael Thiara Jaquelina'),
(209, '52481731', 2, 3, 'RIOS', 'Uma Xiomara'),
(210, '53011487', 2, 3, 'ROFFE', 'Luciano'),
(211, '52738540', 2, 3, 'ROMERO ELIZALDE', 'Lucas Francisco'),
(212, '52738660', 2, 3, 'ROMERO', 'Diego Alejandro Leonel'),
(213, '53163761', 2, 3, 'ROMERO', 'Valentino'),
(214, '52737786', 2, 3, 'RUIZ DIAZ', 'Silvestre Liron Morfeo'),
(215, '52678486', 2, 3, 'RUIZ', 'Julian Ariel'),
(216, '52883572', 2, 3, 'SANCHEZ RAMIREZ', 'Lohan Claudio'),
(217, '53163812', 2, 3, 'SENA', 'Danna Mikaela'),
(218, '52479665', 2, 3, 'TOLEDO', 'Nahiara'),
(219, '53163780', 2, 3, 'VALLEJOS ECKART', 'Valentina'),
(220, '50728359', 3, 1, 'ALAMAN', 'Zaira Sarai'),
(221, '51303979', 3, 1, 'ALFONSO SANCHEZ', 'Angelina Mariel'),
(222, '51290056', 3, 1, 'ARREGIN', 'Nadin Joselin'),
(223, '52239494', 3, 1, 'BARRIENTO', 'Milagros María Antonia'),
(224, '51314461', 3, 1, 'BLANCO', 'Martina Daiana'),
(225, '50584852', 3, 1, 'BRANCATO', 'Martina Paz'),
(226, '52240357', 3, 1, 'DIP ZÁRATE', 'Felipe Martín'),
(227, '52082230', 3, 1, 'DIZANTI BIGANZOLI', 'Joselin Agostina'),
(228, '52163364', 3, 1, 'ESPINOZA', 'David Ernesto'),
(229, '50195728', 3, 1, 'FERNANDEZ QUINTANA', 'Lara Luzmila'),
(230, '52238178', 3, 1, 'GODOY ZACARIAS', 'Aylin Candela'),
(231, '52043013', 3, 1, 'HUARACHI', 'Bautista Ignacio'),
(232, '51466366', 3, 1, 'IBARRA', 'Lucas'),
(233, '50824918', 3, 1, 'JARA', 'Lautaro Ivan'),
(234, '51269946', 3, 1, 'LOPEZ CABRERA MUNIZ', 'Segundo'),
(235, '51289630', 3, 1, 'MALDONADO', 'Xiomara Nathalie'),
(236, '50997474', 3, 1, 'MEDINA', 'Antonella Jazmin'),
(237, '52043029', 3, 1, 'MIEREZ ZABALA', 'Mateo Benjamin'),
(238, '51314414', 3, 1, 'MIÑO PREVE', 'Lourdes Nazareth'),
(239, '50443392', 3, 1, 'MOLINA', 'Oriana Solange'),
(240, '52043966', 3, 1, 'PEREZ RIVERO', 'Blas Ignacio'),
(241, '50112566', 3, 1, 'RETAMAL', 'Alexandro Ian'),
(242, '52098823', 3, 1, 'RODRIGUEZ', 'Matias Maximiliano'),
(243, '51400838', 3, 1, 'ROMERO', 'Luz Constanza'),
(244, '51315475', 3, 1, 'SALAZAR', 'Victoria Irupé'),
(245, '51312403', 3, 1, 'SAN LORENZO MONZON', 'Bayron Leonel'),
(246, '52082005', 3, 1, 'SANDOVAL LANSER', 'Julieta Ludmila'),
(247, '51315487', 3, 1, 'VILLALBA', 'Santino Nicolas'),
(248, '52240175', 3, 2, 'ALARCON', 'Tomas Francisco'),
(249, '50914821', 3, 2, 'BENITEZ', 'Rut Melinda'),
(250, '51314462', 3, 2, 'BLANCO', 'Rocío Mikaela'),
(251, '52237943', 3, 2, 'CABRAL', 'Santiago Gabriel'),
(252, '52043948', 3, 2, 'CARDOZO', 'Gimena Belen'),
(253, '51290913', 3, 2, 'CORREA', 'Valentino'),
(254, '51289350', 3, 2, 'FERNANDEZ TORRES', 'Melanie Agostina'),
(255, '52236226', 3, 2, 'FERNANDEZ', 'Octavio Andres'),
(256, '51315761', 3, 2, 'GOMEZ', 'Samuel German'),
(257, '50427434', 3, 2, 'GONZALEZ', 'Delfina Itati'),
(258, '51466303', 3, 2, 'GONZALEZ', 'Santino Nicolas'),
(259, '52480421', 3, 2, 'JACOB', 'Luz Rebeca Leonor'),
(260, '52238087', 3, 2, 'LAGOMARCINO GARCIA MARTIN', 'Agustin Lionel'),
(261, '52481145', 3, 2, 'LENSINA SERRACANI', 'Bautista Sebastián'),
(262, '52241855', 3, 2, 'MARTINEZ HAYES', 'Emanuel Benjamin'),
(263, '52239188', 3, 2, 'MIRANDA ALDERETE', 'Sabrina Abigail'),
(264, '52240943', 3, 2, 'NOGUERA', 'Julian Emil'),
(265, '52098445', 3, 2, 'OJEDA', 'Delfina Mailén'),
(266, '52239207', 3, 2, 'OVIEDO', 'Nicolas Ezequiel'),
(267, '51315452', 3, 2, 'PAREDES', 'Exequiel Sebastian'),
(268, '52479874', 3, 2, 'PEREZ', 'Angeles Daiara'),
(269, '51537914', 3, 2, 'POSTIGO', 'Lautaro Adrián'),
(270, '52239193', 3, 2, 'RAMIREZ BARRIOS', 'Ian Alexis'),
(271, '51289311', 3, 2, 'SOTOMAYOR ESCOBAR', 'Mariano Nicolas'),
(272, '52480213', 3, 2, 'VELAZQUEZ', 'Juan Martìn'),
(273, '51027947', 3, 3, 'ACOSTA', 'Tatiana Ailen'),
(274, '52240193', 3, 3, 'ALEGRE BEE', 'Joel Agustin'),
(275, '52236291', 3, 3, 'ARIAS', 'Alejo Valentino'),
(276, '50426268', 3, 3, 'BARRIENTO', 'Lautaro Cesar Antonio'),
(277, '52238250', 3, 3, 'BERGER', 'Cristian Federico'),
(278, '51419007', 3, 3, 'BRITEZ', 'Victoria'),
(279, '51006477', 3, 3, 'CHAILE ECHAVARRIA', 'Jose Bautista'),
(280, '49492309', 3, 3, 'CHAVES AMARILLA', 'Lucas Gabriel'),
(281, '50622026', 3, 3, 'ENCINAS', 'Claudio Leonel'),
(282, '52480552', 3, 3, 'FERNANDE', 'Mateo Nicolas'),
(283, '50827048', 3, 3, 'FERNANDEZ', 'Tiziana Elizabeth'),
(284, '51315221', 3, 3, 'GARCIA BLANCO', 'Yatzil Ayelen'),
(285, '52478233', 3, 3, 'GOMEZ CENTURION', 'Camila Agostina Lujan'),
(286, '51265183', 3, 3, 'GOMEZ', 'Noah Federico'),
(287, '51315457', 3, 3, 'LAZOS', 'Kiara Guadalupe'),
(288, '52479691', 3, 3, 'LEIVA DIERINGER', 'Felipe Thomas'),
(289, '51314539', 3, 3, 'MADARIAGA', 'Pedro Francisco'),
(290, '51007332', 3, 3, 'MAIDANA', 'Hana Juliette'),
(291, '51312459', 3, 3, 'MAIDANA', 'Mauro Lisandro'),
(292, '52082242', 3, 3, 'MEILAN', 'Priscila Ana Emilce'),
(293, '51464958', 3, 3, 'OJEDA GIMENEZ', 'Alan Benjamin'),
(294, '52238294', 3, 3, 'PARED GARRIDO', 'Octavio Oscar'),
(295, '51307019', 3, 3, 'PINCHETTI', 'Bastian Alejandro'),
(296, '95760831', 3, 3, 'PINEDA MONGE', 'Sara Carolina'),
(297, '50827440', 3, 3, 'SANCHEZ', 'Brandon Lionel'),
(298, '51028257', 3, 3, 'SUAREZ', 'Jazmin Soledad'),
(299, '51290203', 4, 1, 'ALEGRE MONTIEL', 'Franco Nahuel'),
(300, '51009732', 4, 1, 'ALMIRON', 'Arturo Daniel'),
(301, '51205106', 4, 1, 'ALMIRON', 'Benjamin Ezequiel'),
(302, '51203935', 4, 1, 'ARCE', 'Ainara Maylen'),
(303, '50426891', 4, 1, 'ARRIETA HERNÁNDEZ', 'Tiziano Lionel'),
(304, '50443782', 4, 1, 'BOGARIN PEREYRA', 'Thiago Benjamin'),
(305, '51006496', 4, 1, 'BRAVI PELEATO', 'Erika Lilian'),
(306, '51028262', 4, 1, 'CARDOZO VALENZUELA', 'Maria Micaela'),
(307, '50914687', 4, 1, 'CORDOBA', 'Juliana'),
(308, '51009712', 4, 1, 'DANIELI', 'Exequiel'),
(309, '50827206', 4, 1, 'DE LOS REYES', 'Elika Irene'),
(310, '51009751', 4, 1, 'GARCÍA FERRARO', 'Olivia Itatí'),
(311, '51203870', 4, 1, 'GÓMEZ', 'Brian Dominick'),
(312, '50601101', 4, 1, 'GONZALEZ', 'Avril Julianna'),
(313, '50914939', 4, 1, 'LEZCANO', 'Dante Bautista'),
(314, '49153791', 4, 1, 'LOPEZ', 'Ezequiel Maximiliano'),
(315, '51006635', 4, 1, 'MORINGA', 'Enzo Emanuel'),
(316, '49772910', 4, 1, 'OVIEDO', 'Santiago Gabriel'),
(317, '50620174', 4, 1, 'RETEGUI', 'Ivan Lautaro'),
(318, '51009744', 4, 1, 'RINESSI', 'Delfina Victoria'),
(319, '50621424', 4, 1, 'RODRIGUEZ', 'Bautista Antonio'),
(320, '50601164', 4, 1, 'VELAZQUEZ', 'Tiziano Uriel'),
(321, '50874360', 4, 2, 'AYALA', 'Martina Victoria Del Valle'),
(322, '49452216', 4, 2, 'BENITEZ', 'Sofia Andrea'),
(323, '51204052', 4, 2, 'CANTERO', 'Regina Isabel'),
(324, '50620119', 4, 2, 'CAÑETE PERALTA', 'Carlos Osiel'),
(325, '50827203', 4, 2, 'DIP ZARATE', 'Lara Lucila'),
(326, '49749479', 4, 2, 'FERNANDEZ', 'Juliana Belen'),
(327, '51006603', 4, 2, 'FLORES', 'Lucia Pilar'),
(328, '51009176', 4, 2, 'GAMBOA', 'Martin Josue'),
(329, '50619244', 4, 2, 'IFRAN AYALA', 'Juan Ignacio Martin'),
(330, '50617899', 4, 2, 'MALDONADO RAMIREZ', 'Brenda Itati'),
(331, '50442732', 4, 2, 'MEILAN', 'Francisco Rafael'),
(332, '50827051', 4, 2, 'MENA', 'Lourdes Carolina'),
(333, '49452440', 4, 2, 'PANIAGUA', 'Benjamin Ariel'),
(334, '51006671', 4, 2, 'PEREZ ALEGRE', 'Bautista'),
(335, '51009687', 4, 2, 'QUINTANA', 'Luna Nahir'),
(336, '50443777', 4, 2, 'QUINTANA', 'Mia Josefina'),
(337, '50443996', 4, 2, 'QUINTANA', 'Zahira Yazmin'),
(338, '50481080', 4, 2, 'ROSTAN FORTE', 'Luna Serena'),
(339, '50620107', 4, 2, 'SCHIAVI', 'Joaquin Ernesto'),
(340, '51006072', 4, 2, 'STRUCIAT', 'Gonzalo Nahuel'),
(341, '51006481', 4, 2, 'VALLEJOS', 'Mia Selena Itati'),
(342, '50620122', 4, 2, 'VOGELMANN', 'Nahiara Aylin'),
(343, '50601920', 4, 2, 'ZAMUDIO ESCALANTE', 'Diego Benjamin'),
(344, '49771441', 5, 1, 'ALFONZO ZIBELMAN', 'Dana Agustina'),
(345, '48787579', 5, 1, 'CANO', 'Tiara Anabel'),
(346, '50196090', 5, 1, 'CESPEDES', 'Pia Trinidad'),
(347, '49749149', 5, 1, 'CUELLAR', 'Jimena Paola'),
(348, '49772677', 5, 1, 'DORMELES ACUÑA', 'Milton Adrian'),
(349, '49749546', 5, 1, 'ESTEPA', 'Aaron Dario'),
(350, '49879539', 5, 1, 'FLORES', 'Camila Belen'),
(351, '49452372', 5, 1, 'LUQUE ZACARIAS', 'Alvaro Jose'),
(352, '49879903', 5, 1, 'MAIDANA', 'Valentina Eugenia'),
(353, '49749132', 5, 1, 'MARTINEZ', 'Santiago Alejandro Fermin'),
(354, '49879524', 5, 1, 'MIÑO PREVE', 'Ivan'),
(355, '49771359', 5, 1, 'NUÑEZ VALLEJOS', 'Anna Paula'),
(356, '50196652', 5, 1, 'ÑAÑEZ AGUIRRE', 'Guadalupe Milagros'),
(357, '49326654', 5, 1, 'OJEDA CENTURION', 'Bautista Agustin'),
(358, '50197284', 5, 1, 'PALACIOS', 'Pablo Thomás'),
(359, '49772942', 5, 1, 'PALOMERO', 'Iam Bautista'),
(360, '50197568', 5, 1, 'RAMIREZ CORDOBE', 'Bianca Itati'),
(361, '49769911', 5, 1, 'RODRIGUEZ', 'Lucio Gabriel'),
(362, '49749459', 5, 1, 'ROMERO', 'Mia Louisana'),
(363, '49452098', 5, 1, 'ROMERO', 'Uriel Julián'),
(364, '49213669', 5, 1, 'SANCHEZ', 'Lisandro Laureano'),
(365, '50040929', 5, 1, 'SERRANO', 'Maria Delfina'),
(366, '49480537', 5, 1, 'SUAREZ PARED', 'Dante Teodoro'),
(367, '48615379', 5, 1, 'VALLEJOS', 'Elias Agustin'),
(368, '50123861', 5, 1, 'ZACARIAS', 'Pamela Maria Itati'),
(369, '49879232', 5, 2, 'AREVALO', 'Candela Virginia'),
(370, '49491446', 5, 2, 'BAEZ', 'Layla Jazmin'),
(371, '49325316', 5, 2, 'CANTERO', 'Mia Nahiara Aymara'),
(372, '49749875', 5, 2, 'CORREA MORALES', 'Candela Florencia'),
(373, '50196724', 5, 2, 'ESPINDOLA VARGAS', 'Luciano Javier'),
(374, '50062407', 5, 2, 'FERNANDEZ', 'Joaquin Uriel'),
(375, '48342167', 5, 2, 'FLORES', 'Guillermo Daniel'),
(376, '50196139', 5, 2, 'GIMENEZ', 'Melina Victoria'),
(377, '49771467', 5, 2, 'GONZALEZ MORATORIO', 'Victoria Lucila'),
(378, '49451223', 5, 2, 'GONZALEZ', 'Leandro Valentin'),
(379, '50062143', 5, 2, 'JARA RIVERO', 'Cyara Luz'),
(380, '48789420', 5, 2, 'LEIVA DIERINGER', 'Facundo Damian'),
(381, '48685333', 5, 2, 'LUBARY BARRIOS', 'Pilar'),
(382, '50425845', 5, 2, 'LUGO', 'Tiziana Valentina'),
(383, '48734851', 5, 2, 'MOLINA', 'Daira Ayelén'),
(384, '50199334', 5, 2, 'MONZON ARANDA', 'Jeremy Emanuel'),
(385, '49771267', 5, 2, 'NUÑEZ', 'Emiliano Joel'),
(386, '50196600', 5, 2, 'OJEDA', 'Tatiana Ayelen'),
(387, '49326855', 5, 2, 'PERALTA GONZALEZ', 'Candela Elizabeth'),
(388, '49771368', 5, 2, 'PEREIRA', 'Axel Thiago Bautista'),
(389, '48788719', 5, 2, 'QUEVEDO', 'Maia Ailin'),
(390, '49879158', 5, 2, 'ROMERO', 'Margarita Stefanía'),
(391, '50425930', 5, 2, 'SAVARESSE GALARZA', 'Selena'),
(392, '50197280', 5, 2, 'VILLARREAL YAYA', 'Ignacio Bautista'),
(393, '49213432', 6, 1, 'ALEGRE ACOSTA', 'Thiago Gabriel'),
(394, '49211901', 6, 1, 'ALFONZO', 'Daniela Abril'),
(395, '48788649', 6, 1, 'BENITEZ', 'Martina Juliana'),
(396, '48787596', 6, 1, 'FERNANDEZ AGUIRRE', 'Fabrizio Leandro'),
(397, '49275214', 6, 1, 'GARCIA', 'Agustina Irupe'),
(398, '48686383', 6, 1, 'GARCÍA', 'Cesia Narela'),
(399, '48734930', 6, 1, 'GOMEZ ORTIZ', 'Valentina Itati'),
(400, '49153129', 6, 1, 'GONZALES', 'Francisco Agustin'),
(401, '49275577', 6, 1, 'LIBORSI', 'Dasha Martina'),
(402, '49213827', 6, 1, 'MACHUCA', 'Valentina Liseth'),
(403, '49210501', 6, 1, 'MARECO', 'Loana Melany'),
(404, '48552665', 6, 1, 'MARTINEZ DA SILVEYRA', 'Lucas Agustin'),
(405, '49152246', 6, 1, 'MARTINEZ GOMEZ', 'Naomi Nahir'),
(406, '48552117', 6, 1, 'MONTIEL', 'Mariano Emmanuel'),
(407, '49153344', 6, 1, 'NAVARRET ARANDA', 'Valentin'),
(408, '49155146', 6, 1, 'OJEDA PEDELHEZ', 'Carla Victoria'),
(409, '48840890', 6, 1, 'RODRIGUEZ', 'Sheila Natasha'),
(410, '49325822', 6, 1, 'SAADE FALCÓN', 'Matías Uriel'),
(411, '47421505', 6, 1, 'SILVA', 'Matias Joaquin'),
(412, '48685332', 6, 1, 'VALDEZ GOMEZ', 'Tobias Cesar'),
(413, '48552105', 6, 2, 'ACEVEDO GONZALEZ', 'Thiago Joaquin'),
(414, '49153331', 6, 2, 'BENITEZ SOSA', 'Camila Rita'),
(415, '49213576', 6, 2, 'CARDOZO', 'Mateo David'),
(416, '48342765', 6, 2, 'COLMAN MAIDANA', 'Paola Celeste'),
(417, '49325463', 6, 2, 'CRISTALDO DUARTE', 'Ignacio Sebastian'),
(418, '49211924', 6, 2, 'DE ASMUNDIS SANCHEZ', 'Máximo Patricio'),
(419, '49492177', 6, 2, 'DE ASMUNDIS', 'Alejandro Alberto'),
(420, '49211966', 6, 2, 'FRANCO', 'Dante Lionel'),
(421, '48734792', 6, 2, 'GIRAUD', 'Dante Raul'),
(422, '49528258', 6, 2, 'GOMEZ MORALES', 'Juan Cruz'),
(423, '49210638', 6, 2, 'LEDESMA', 'Franco Leonel'),
(424, '49326490', 6, 2, 'LEYES', 'Rodrigo Sebastian'),
(425, '49326774', 6, 2, 'MAIDANA', 'Luisana Jazmin De Lujan'),
(426, '48194869', 6, 2, 'PASTOR', 'Lucio Benjamín'),
(427, '49152300', 6, 2, 'PEREYRA', 'Tomás Gabriel'),
(428, '47615129', 6, 2, 'RIOS', 'Axel Nicolas'),
(429, '49326404', 6, 2, 'ROMERO', 'Antonella'),
(430, '49210544', 6, 2, 'ROMERO', 'Thiago Nicolas'),
(431, '48952164', 6, 2, 'ROSTAN FORTE', 'Alma Agustina'),
(432, '49039503', 6, 2, 'TORRES', 'Maria Victoria'),
(433, '49153516', 6, 2, 'URQUIZA', 'Tiara Aylen'),
(434, '48734728', 6, 2, 'VALLEJOS', 'Octavio Luis'),
(435, '48616238', 7, 1, 'ALEGRE ROMERO', 'Fiorella Madeleine'),
(436, '47990981', 7, 1, 'ALVAREZ', 'Bautista Benjamin'),
(437, '47969658', 7, 1, 'AYALA CODIGONI', 'Facundo Gabriel'),
(438, '48686066', 7, 1, 'BRIZUELA FALCON', 'Gaston Emanuel'),
(439, '48614617', 7, 1, 'CABALLERO', 'Arianna Yanela'),
(440, '47990961', 7, 1, 'CASTRO', 'Guillermo Tomas'),
(441, '48344157', 7, 1, 'ESPINDOLA VARGAS', 'Ignacio Daniel'),
(442, '49326936', 7, 1, 'FALCON BUBANS', 'Thiago Luis Ezequiel'),
(443, '48131492', 7, 1, 'GARCIA', 'Ivan Valentino'),
(444, '48399978', 7, 1, 'GODOY', 'Ruben Leonardo'),
(445, '48686321', 7, 1, 'GONZALEZ CORONEL', 'Luciano Augusto'),
(446, '48132493', 7, 1, 'LAFFONT MEZA', 'Pablo Enrique'),
(447, '48615384', 7, 1, 'LEIVA', 'Patricio Ignacio'),
(448, '47896049', 7, 1, 'MENDOZA', 'Fabricio Federico'),
(449, '48314108', 7, 1, 'RODRIGUEZ FAIJO', 'Sebastian Alejandro'),
(450, '48342155', 7, 1, 'ROMERO', 'José Nahuel'),
(451, '48133347', 7, 1, 'RUIDIAZ NAVARRO', 'Milagros Maria'),
(452, '48342233', 7, 2, 'AGUIRRE BARRIOS', 'Juan Francisco'),
(453, '48343302', 7, 2, 'ALVAREZ', 'Irupé'),
(454, '48729737', 7, 2, 'ANTOÑADIS', 'Nerina Ruth Abigail'),
(455, '46601755', 7, 2, 'BARRIOS VELASQUES', 'Santiago Nathanael'),
(456, '48545447', 7, 2, 'BLANCO', 'Mateo Benjamin'),
(457, '46717933', 7, 2, 'BUSS', 'Leandro Samuel'),
(458, '48686093', 7, 2, 'CARDOZO', 'Franco Lionel'),
(459, '48552131', 7, 2, 'DOMATO', 'Antonella Magali'),
(460, '48192348', 7, 2, 'ESCOBAR', 'Laura Noemi'),
(461, '48260523', 7, 2, 'FALCON AGUIRRE', 'Juan Ignacio'),
(462, '48131479', 7, 2, 'FERNANDEZ GONZALEZ', 'Rita Milagro'),
(463, '48342886', 7, 2, 'FLORES', 'Esteban Sebastian'),
(464, '48260534', 7, 2, 'GOMEZ ARCE', 'Angel Martin'),
(465, '48132977', 7, 2, 'LEYES', 'Araceli Elizabeth'),
(466, '48553780', 7, 2, 'MARTINEZ', 'Bautista Jesus'),
(467, '48191595', 7, 2, 'MORLIO', 'Ciro Lionel'),
(468, '48261364', 7, 2, 'ORGOÑ BIT CHAKOCH', 'Leia Eliana'),
(469, '48262395', 7, 2, 'OVIEDO', 'Adolfo Joaquín'),
(470, '48260646', 7, 2, 'ROMERO FIGUEREDO', 'Morena Jazmin'),
(471, '50195761', 7, 2, 'SOLER', 'Estefania Abigail'),
(472, '48552822', 7, 2, 'THOUZEAU', 'Eileen Guadalupe'),
(473, '46244955', 7, 2, 'USINGER LEIZ', 'Lautaro German');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos`
--

CREATE TABLE `cargos` (
  `id_cargo` int(11) NOT NULL,
  `nombre_cargo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `cargos`
--

INSERT INTO `cargos` (`id_cargo`, `nombre_cargo`) VALUES
(1, 'Presidente'),
(5, 'Secretario de Asuntos Estudiantiles'),
(4, 'Secretario de Cultura, Deportes y Recreación'),
(3, 'Secretario de Finanzas'),
(7, 'Secretario de Gestión Comunitaria'),
(6, 'Secretario de Prensa, Difusión y Relaciones Públicas'),
(2, 'Secretario General');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargos_por_lista`
--

CREATE TABLE `cargos_por_lista` (
  `id_cargo_lista` int(11) NOT NULL,
  `id_lista` int(11) NOT NULL,
  `id_cargo` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instancia_electoral`
--

CREATE TABLE `instancia_electoral` (
  `id_instancia` int(11) NOT NULL,
  `anio` int(11) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `activa` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `instancia_electoral`
--

INSERT INTO `instancia_electoral` (`id_instancia`, `anio`, `descripcion`, `activa`) VALUES
(1, 2026, 'ELECCION \"CENTRO DE ESTUDIANTES\" ', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lista`
--

CREATE TABLE `lista` (
  `id_lista` int(11) NOT NULL,
  `id_instancia` int(11) NOT NULL,
  `numero_lista` int(11) NOT NULL,
  `nombre_lista` varchar(100) NOT NULL,
  `logo_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `lista`
--

INSERT INTO `lista` (`id_lista`, `id_instancia`, `numero_lista`, `nombre_lista`, `logo_url`) VALUES
(1, 1, 1, 'Prueba 1', NULL),
(2, 1, 2, 'Prueba 2', NULL),
(3, 1, 3, 'Prueba 3', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `padron_electoral`
--

CREATE TABLE `padron_electoral` (
  `id_padron` int(11) NOT NULL,
  `id_instancia` int(11) NOT NULL,
  `id_alumno` int(11) NOT NULL,
  `estado_voto` tinyint(1) DEFAULT 0,
  `fecha_hora_voto` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `padron_electoral`
--

INSERT INTO `padron_electoral` (`id_padron`, `id_instancia`, `id_alumno`, `estado_voto`, `fecha_hora_voto`) VALUES
(2, 1, 1, 0, NULL),
(3, 1, 2, 0, NULL),
(4, 1, 3, 0, NULL),
(5, 1, 4, 0, NULL),
(6, 1, 5, 0, NULL),
(7, 1, 6, 0, NULL),
(8, 1, 7, 0, NULL),
(9, 1, 8, 0, NULL),
(10, 1, 9, 0, NULL),
(11, 1, 10, 0, NULL),
(12, 1, 11, 0, NULL),
(13, 1, 12, 0, NULL),
(14, 1, 13, 0, NULL),
(15, 1, 14, 0, NULL),
(16, 1, 15, 0, NULL),
(17, 1, 16, 0, NULL),
(18, 1, 17, 0, NULL),
(19, 1, 18, 0, NULL),
(20, 1, 19, 0, NULL),
(21, 1, 20, 0, NULL),
(22, 1, 21, 0, NULL),
(23, 1, 22, 0, NULL),
(24, 1, 23, 0, NULL),
(25, 1, 24, 0, NULL),
(26, 1, 25, 0, NULL),
(27, 1, 26, 0, NULL),
(28, 1, 27, 0, NULL),
(29, 1, 28, 0, NULL),
(30, 1, 29, 0, NULL),
(31, 1, 30, 0, NULL),
(32, 1, 31, 0, NULL),
(33, 1, 32, 0, NULL),
(34, 1, 33, 0, NULL),
(35, 1, 34, 0, NULL),
(36, 1, 35, 0, NULL),
(37, 1, 36, 0, NULL),
(38, 1, 37, 0, NULL),
(39, 1, 38, 0, NULL),
(40, 1, 39, 0, NULL),
(41, 1, 40, 0, NULL),
(42, 1, 41, 0, NULL),
(43, 1, 42, 0, NULL),
(44, 1, 43, 0, NULL),
(45, 1, 44, 0, NULL),
(46, 1, 45, 0, NULL),
(47, 1, 46, 0, NULL),
(48, 1, 47, 0, NULL),
(49, 1, 48, 0, NULL),
(50, 1, 49, 0, NULL),
(51, 1, 50, 0, NULL),
(52, 1, 51, 0, NULL),
(53, 1, 52, 0, NULL),
(54, 1, 53, 0, NULL),
(55, 1, 54, 0, NULL),
(56, 1, 55, 0, NULL),
(57, 1, 56, 0, NULL),
(58, 1, 57, 0, NULL),
(59, 1, 58, 0, NULL),
(60, 1, 59, 0, NULL),
(61, 1, 60, 0, NULL),
(62, 1, 61, 0, NULL),
(63, 1, 62, 0, NULL),
(64, 1, 63, 0, NULL),
(65, 1, 64, 0, NULL),
(66, 1, 65, 0, NULL),
(67, 1, 66, 0, NULL),
(68, 1, 67, 0, NULL),
(69, 1, 68, 0, NULL),
(70, 1, 69, 0, NULL),
(71, 1, 70, 0, NULL),
(72, 1, 71, 0, NULL),
(73, 1, 72, 0, NULL),
(74, 1, 73, 0, NULL),
(75, 1, 74, 0, NULL),
(76, 1, 75, 0, NULL),
(77, 1, 76, 0, NULL),
(78, 1, 77, 0, NULL),
(79, 1, 78, 0, NULL),
(80, 1, 79, 0, NULL),
(81, 1, 80, 0, NULL),
(82, 1, 81, 0, NULL),
(83, 1, 82, 0, NULL),
(84, 1, 83, 0, NULL),
(85, 1, 84, 0, NULL),
(86, 1, 85, 0, NULL),
(87, 1, 86, 0, NULL),
(88, 1, 87, 0, NULL),
(89, 1, 88, 0, NULL),
(90, 1, 89, 0, NULL),
(91, 1, 90, 0, NULL),
(92, 1, 91, 0, NULL),
(93, 1, 92, 0, NULL),
(94, 1, 93, 0, NULL),
(95, 1, 94, 0, NULL),
(96, 1, 95, 0, NULL),
(97, 1, 96, 0, NULL),
(98, 1, 97, 0, NULL),
(99, 1, 98, 0, NULL),
(100, 1, 99, 0, NULL),
(101, 1, 100, 0, NULL),
(102, 1, 101, 0, NULL),
(103, 1, 102, 0, NULL),
(104, 1, 103, 0, NULL),
(105, 1, 104, 0, NULL),
(106, 1, 105, 0, NULL),
(107, 1, 106, 0, NULL),
(108, 1, 107, 0, NULL),
(109, 1, 108, 0, NULL),
(110, 1, 109, 0, NULL),
(111, 1, 110, 0, NULL),
(112, 1, 111, 0, NULL),
(113, 1, 112, 0, NULL),
(114, 1, 113, 0, NULL),
(115, 1, 114, 0, NULL),
(116, 1, 115, 0, NULL),
(117, 1, 116, 0, NULL),
(118, 1, 117, 0, NULL),
(119, 1, 118, 0, NULL),
(120, 1, 119, 0, NULL),
(121, 1, 120, 0, NULL),
(122, 1, 121, 0, NULL),
(123, 1, 122, 0, NULL),
(124, 1, 123, 0, NULL),
(125, 1, 124, 0, NULL),
(126, 1, 125, 0, NULL),
(127, 1, 126, 0, NULL),
(128, 1, 127, 0, NULL),
(129, 1, 128, 0, NULL),
(130, 1, 129, 0, NULL),
(131, 1, 130, 0, NULL),
(132, 1, 131, 0, NULL),
(133, 1, 132, 0, NULL),
(134, 1, 133, 0, NULL),
(135, 1, 134, 0, NULL),
(136, 1, 135, 0, NULL),
(137, 1, 136, 0, NULL),
(138, 1, 137, 0, NULL),
(139, 1, 138, 0, NULL),
(140, 1, 139, 0, NULL),
(141, 1, 140, 0, NULL),
(142, 1, 141, 0, NULL),
(143, 1, 142, 0, NULL),
(144, 1, 143, 0, NULL),
(145, 1, 144, 0, NULL),
(146, 1, 145, 0, NULL),
(147, 1, 146, 0, NULL),
(148, 1, 147, 0, NULL),
(149, 1, 148, 0, NULL),
(150, 1, 149, 0, NULL),
(151, 1, 150, 0, NULL),
(152, 1, 151, 0, NULL),
(153, 1, 152, 0, NULL),
(154, 1, 153, 0, NULL),
(155, 1, 154, 0, NULL),
(156, 1, 155, 0, NULL),
(157, 1, 156, 0, NULL),
(158, 1, 157, 0, NULL),
(159, 1, 158, 0, NULL),
(160, 1, 159, 0, NULL),
(161, 1, 160, 0, NULL),
(162, 1, 161, 0, NULL),
(163, 1, 162, 0, NULL),
(164, 1, 163, 0, NULL),
(165, 1, 164, 0, NULL),
(166, 1, 165, 0, NULL),
(167, 1, 166, 0, NULL),
(168, 1, 167, 0, NULL),
(169, 1, 168, 0, NULL),
(170, 1, 169, 0, NULL),
(171, 1, 170, 0, NULL),
(172, 1, 171, 0, NULL),
(173, 1, 172, 0, NULL),
(174, 1, 173, 0, NULL),
(175, 1, 174, 0, NULL),
(176, 1, 175, 0, NULL),
(177, 1, 176, 0, NULL),
(178, 1, 177, 0, NULL),
(179, 1, 178, 0, NULL),
(180, 1, 179, 0, NULL),
(181, 1, 180, 0, NULL),
(182, 1, 181, 0, NULL),
(183, 1, 182, 0, NULL),
(184, 1, 183, 0, NULL),
(185, 1, 184, 0, NULL),
(186, 1, 185, 0, NULL),
(187, 1, 186, 0, NULL),
(188, 1, 187, 0, NULL),
(189, 1, 188, 0, NULL),
(190, 1, 189, 0, NULL),
(191, 1, 190, 0, NULL),
(192, 1, 191, 0, NULL),
(193, 1, 192, 0, NULL),
(194, 1, 193, 0, NULL),
(195, 1, 194, 0, NULL),
(196, 1, 195, 0, NULL),
(197, 1, 196, 0, NULL),
(198, 1, 197, 0, NULL),
(199, 1, 198, 0, NULL),
(200, 1, 199, 0, NULL),
(201, 1, 200, 0, NULL),
(202, 1, 201, 0, NULL),
(203, 1, 202, 0, NULL),
(204, 1, 203, 0, NULL),
(205, 1, 204, 0, NULL),
(206, 1, 205, 0, NULL),
(207, 1, 206, 0, NULL),
(208, 1, 207, 0, NULL),
(209, 1, 208, 0, NULL),
(210, 1, 209, 0, NULL),
(211, 1, 210, 0, NULL),
(212, 1, 211, 0, NULL),
(213, 1, 212, 0, NULL),
(214, 1, 213, 0, NULL),
(215, 1, 214, 0, NULL),
(216, 1, 215, 0, NULL),
(217, 1, 216, 0, NULL),
(218, 1, 217, 0, NULL),
(219, 1, 218, 0, NULL),
(220, 1, 219, 0, NULL),
(221, 1, 220, 0, NULL),
(222, 1, 221, 0, NULL),
(223, 1, 222, 0, NULL),
(224, 1, 223, 0, NULL),
(225, 1, 224, 0, NULL),
(226, 1, 225, 0, NULL),
(227, 1, 226, 0, NULL),
(228, 1, 227, 0, NULL),
(229, 1, 228, 0, NULL),
(230, 1, 229, 0, NULL),
(231, 1, 230, 0, NULL),
(232, 1, 231, 0, NULL),
(233, 1, 232, 0, NULL),
(234, 1, 233, 0, NULL),
(235, 1, 234, 0, NULL),
(236, 1, 235, 0, NULL),
(237, 1, 236, 0, NULL),
(238, 1, 237, 0, NULL),
(239, 1, 238, 0, NULL),
(240, 1, 239, 0, NULL),
(241, 1, 240, 0, NULL),
(242, 1, 241, 0, NULL),
(243, 1, 242, 0, NULL),
(244, 1, 243, 0, NULL),
(245, 1, 244, 0, NULL),
(246, 1, 245, 0, NULL),
(247, 1, 246, 0, NULL),
(248, 1, 247, 0, NULL),
(249, 1, 248, 0, NULL),
(250, 1, 249, 0, NULL),
(251, 1, 250, 0, NULL),
(252, 1, 251, 0, NULL),
(253, 1, 252, 0, NULL),
(254, 1, 253, 0, NULL),
(255, 1, 254, 0, NULL),
(256, 1, 255, 0, NULL),
(257, 1, 256, 0, NULL),
(258, 1, 257, 0, NULL),
(259, 1, 258, 0, NULL),
(260, 1, 259, 0, NULL),
(261, 1, 260, 0, NULL),
(262, 1, 261, 0, NULL),
(263, 1, 262, 0, NULL),
(264, 1, 263, 0, NULL),
(265, 1, 264, 0, NULL),
(266, 1, 265, 0, NULL),
(267, 1, 266, 0, NULL),
(268, 1, 267, 0, NULL),
(269, 1, 268, 0, NULL),
(270, 1, 269, 0, NULL),
(271, 1, 270, 0, NULL),
(272, 1, 271, 0, NULL),
(273, 1, 272, 0, NULL),
(274, 1, 273, 0, NULL),
(275, 1, 274, 0, NULL),
(276, 1, 275, 0, NULL),
(277, 1, 276, 0, NULL),
(278, 1, 277, 0, NULL),
(279, 1, 278, 0, NULL),
(280, 1, 279, 0, NULL),
(281, 1, 280, 0, NULL),
(282, 1, 281, 0, NULL),
(283, 1, 282, 0, NULL),
(284, 1, 283, 0, NULL),
(285, 1, 284, 0, NULL),
(286, 1, 285, 0, NULL),
(287, 1, 286, 0, NULL),
(288, 1, 287, 0, NULL),
(289, 1, 288, 0, NULL),
(290, 1, 289, 0, NULL),
(291, 1, 290, 0, NULL),
(292, 1, 291, 0, NULL),
(293, 1, 292, 0, NULL),
(294, 1, 293, 0, NULL),
(295, 1, 294, 0, NULL),
(296, 1, 295, 0, NULL),
(297, 1, 296, 0, NULL),
(298, 1, 297, 0, NULL),
(299, 1, 298, 0, NULL),
(300, 1, 299, 0, NULL),
(301, 1, 300, 0, NULL),
(302, 1, 301, 0, NULL),
(303, 1, 302, 0, NULL),
(304, 1, 303, 0, NULL),
(305, 1, 304, 0, NULL),
(306, 1, 305, 0, NULL),
(307, 1, 306, 0, NULL),
(308, 1, 307, 0, NULL),
(309, 1, 308, 0, NULL),
(310, 1, 309, 0, NULL),
(311, 1, 310, 0, NULL),
(312, 1, 311, 0, NULL),
(313, 1, 312, 0, NULL),
(314, 1, 313, 0, NULL),
(315, 1, 314, 0, NULL),
(316, 1, 315, 0, NULL),
(317, 1, 316, 0, NULL),
(318, 1, 317, 0, NULL),
(319, 1, 318, 0, NULL),
(320, 1, 319, 0, NULL),
(321, 1, 320, 0, NULL),
(322, 1, 321, 0, NULL),
(323, 1, 322, 0, NULL),
(324, 1, 323, 0, NULL),
(325, 1, 324, 0, NULL),
(326, 1, 325, 0, NULL),
(327, 1, 326, 0, NULL),
(328, 1, 327, 0, NULL),
(329, 1, 328, 0, NULL),
(330, 1, 329, 0, NULL),
(331, 1, 330, 0, NULL),
(332, 1, 331, 0, NULL),
(333, 1, 332, 0, NULL),
(334, 1, 333, 0, NULL),
(335, 1, 334, 0, NULL),
(336, 1, 335, 0, NULL),
(337, 1, 336, 0, NULL),
(338, 1, 337, 0, NULL),
(339, 1, 338, 0, NULL),
(340, 1, 339, 0, NULL),
(341, 1, 340, 0, NULL),
(342, 1, 341, 0, NULL),
(343, 1, 342, 0, NULL),
(344, 1, 343, 0, NULL),
(345, 1, 344, 0, NULL),
(346, 1, 345, 0, NULL),
(347, 1, 346, 0, NULL),
(348, 1, 347, 0, NULL),
(349, 1, 348, 0, NULL),
(350, 1, 349, 0, NULL),
(351, 1, 350, 0, NULL),
(352, 1, 351, 0, NULL),
(353, 1, 352, 0, NULL),
(354, 1, 353, 0, NULL),
(355, 1, 354, 0, NULL),
(356, 1, 355, 0, NULL),
(357, 1, 356, 0, NULL),
(358, 1, 357, 0, NULL),
(359, 1, 358, 0, NULL),
(360, 1, 359, 0, NULL),
(361, 1, 360, 0, NULL),
(362, 1, 361, 0, NULL),
(363, 1, 362, 0, NULL),
(364, 1, 363, 0, NULL),
(365, 1, 364, 0, NULL),
(366, 1, 365, 0, NULL),
(367, 1, 366, 0, NULL),
(368, 1, 367, 0, NULL),
(369, 1, 368, 0, NULL),
(370, 1, 369, 0, NULL),
(371, 1, 370, 0, NULL),
(372, 1, 371, 0, NULL),
(373, 1, 372, 0, NULL),
(374, 1, 373, 0, NULL),
(375, 1, 374, 0, NULL),
(376, 1, 375, 0, NULL),
(377, 1, 376, 0, NULL),
(378, 1, 377, 0, NULL),
(379, 1, 378, 0, NULL),
(380, 1, 379, 0, NULL),
(381, 1, 380, 0, NULL),
(382, 1, 381, 0, NULL),
(383, 1, 382, 0, NULL),
(384, 1, 383, 0, NULL),
(385, 1, 384, 0, NULL),
(386, 1, 385, 0, NULL),
(387, 1, 386, 0, NULL),
(388, 1, 387, 0, NULL),
(389, 1, 388, 0, NULL),
(390, 1, 389, 0, NULL),
(391, 1, 390, 0, NULL),
(392, 1, 391, 0, NULL),
(393, 1, 392, 0, NULL),
(394, 1, 393, 0, NULL),
(395, 1, 394, 0, NULL),
(396, 1, 395, 1, '2026-08-31 13:58:59'),
(397, 1, 396, 0, NULL),
(398, 1, 397, 0, NULL),
(399, 1, 398, 1, '2026-08-31 20:52:29'),
(400, 1, 399, 0, NULL),
(401, 1, 400, 0, NULL),
(402, 1, 401, 0, NULL),
(403, 1, 402, 0, NULL),
(404, 1, 403, 0, NULL),
(405, 1, 404, 1, '2026-08-31 14:30:42'),
(406, 1, 405, 0, NULL),
(407, 1, 406, 1, '2026-08-31 13:59:28'),
(408, 1, 407, 0, NULL),
(409, 1, 408, 0, NULL),
(410, 1, 409, 0, NULL),
(411, 1, 410, 0, NULL),
(412, 1, 411, 0, NULL),
(413, 1, 412, 0, NULL),
(414, 1, 413, 0, NULL),
(415, 1, 414, 0, NULL),
(416, 1, 415, 0, NULL),
(417, 1, 416, 0, NULL),
(418, 1, 417, 0, NULL),
(419, 1, 418, 0, NULL),
(420, 1, 419, 0, NULL),
(421, 1, 420, 0, NULL),
(422, 1, 421, 0, NULL),
(423, 1, 422, 0, NULL),
(424, 1, 423, 0, NULL),
(425, 1, 424, 0, NULL),
(426, 1, 425, 0, NULL),
(427, 1, 426, 0, NULL),
(428, 1, 427, 0, NULL),
(429, 1, 428, 0, NULL),
(430, 1, 429, 0, NULL),
(431, 1, 430, 0, NULL),
(432, 1, 431, 0, NULL),
(433, 1, 432, 0, NULL),
(434, 1, 433, 0, NULL),
(435, 1, 434, 0, NULL),
(436, 1, 435, 0, NULL),
(437, 1, 436, 0, NULL),
(438, 1, 437, 0, NULL),
(439, 1, 438, 0, NULL),
(440, 1, 439, 0, NULL),
(441, 1, 440, 0, NULL),
(442, 1, 441, 0, NULL),
(443, 1, 442, 0, NULL),
(444, 1, 443, 0, NULL),
(445, 1, 444, 0, NULL),
(446, 1, 445, 0, NULL),
(447, 1, 446, 0, NULL),
(448, 1, 447, 0, NULL),
(449, 1, 448, 0, NULL),
(450, 1, 449, 0, NULL),
(451, 1, 450, 0, NULL),
(452, 1, 451, 0, NULL),
(453, 1, 452, 0, NULL),
(454, 1, 453, 0, NULL),
(455, 1, 454, 0, NULL),
(456, 1, 455, 0, NULL),
(457, 1, 456, 0, NULL),
(458, 1, 457, 0, NULL),
(459, 1, 458, 0, NULL),
(460, 1, 459, 0, NULL),
(461, 1, 460, 0, NULL),
(462, 1, 461, 0, NULL),
(463, 1, 462, 0, NULL),
(464, 1, 463, 0, NULL),
(465, 1, 464, 0, NULL),
(466, 1, 465, 0, NULL),
(467, 1, 466, 0, NULL),
(468, 1, 467, 0, NULL),
(469, 1, 468, 0, NULL),
(470, 1, 469, 0, NULL),
(471, 1, 470, 0, NULL),
(472, 1, 471, 0, NULL),
(473, 1, 472, 0, NULL),
(474, 1, 473, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `votos`
--

CREATE TABLE `votos` (
  `id_voto` int(11) NOT NULL,
  `id_instancia` int(11) NOT NULL,
  `id_lista` int(11) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id_alumno`),
  ADD UNIQUE KEY `uk_dni` (`dni`);

--
-- Indices de la tabla `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id_cargo`),
  ADD UNIQUE KEY `uk_nombre_cargo` (`nombre_cargo`);

--
-- Indices de la tabla `cargos_por_lista`
--
ALTER TABLE `cargos_por_lista`
  ADD PRIMARY KEY (`id_cargo_lista`),
  ADD UNIQUE KEY `uk_lista_cargo` (`id_lista`,`id_cargo`),
  ADD KEY `fk_cpl_cargo` (`id_cargo`),
  ADD KEY `fk_cpl_alumno` (`id_alumno`);

--
-- Indices de la tabla `instancia_electoral`
--
ALTER TABLE `instancia_electoral`
  ADD PRIMARY KEY (`id_instancia`),
  ADD UNIQUE KEY `uk_anio` (`anio`);

--
-- Indices de la tabla `lista`
--
ALTER TABLE `lista`
  ADD PRIMARY KEY (`id_lista`),
  ADD UNIQUE KEY `uk_instancia_lista` (`id_instancia`,`numero_lista`);

--
-- Indices de la tabla `padron_electoral`
--
ALTER TABLE `padron_electoral`
  ADD PRIMARY KEY (`id_padron`),
  ADD UNIQUE KEY `uk_instancia_alumno` (`id_instancia`,`id_alumno`),
  ADD KEY `fk_padron_alumno` (`id_alumno`);

--
-- Indices de la tabla `votos`
--
ALTER TABLE `votos`
  ADD PRIMARY KEY (`id_voto`),
  ADD KEY `fk_voto_instancia` (`id_instancia`),
  ADD KEY `fk_voto_lista` (`id_lista`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id_alumno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=474;

--
-- AUTO_INCREMENT de la tabla `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `cargos_por_lista`
--
ALTER TABLE `cargos_por_lista`
  MODIFY `id_cargo_lista` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `instancia_electoral`
--
ALTER TABLE `instancia_electoral`
  MODIFY `id_instancia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `lista`
--
ALTER TABLE `lista`
  MODIFY `id_lista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `padron_electoral`
--
ALTER TABLE `padron_electoral`
  MODIFY `id_padron` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=475;

--
-- AUTO_INCREMENT de la tabla `votos`
--
ALTER TABLE `votos`
  MODIFY `id_voto` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cargos_por_lista`
--
ALTER TABLE `cargos_por_lista`
  ADD CONSTRAINT `fk_cpl_alumno` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`),
  ADD CONSTRAINT `fk_cpl_cargo` FOREIGN KEY (`id_cargo`) REFERENCES `cargos` (`id_cargo`),
  ADD CONSTRAINT `fk_cpl_lista` FOREIGN KEY (`id_lista`) REFERENCES `lista` (`id_lista`) ON DELETE CASCADE;

--
-- Filtros para la tabla `lista`
--
ALTER TABLE `lista`
  ADD CONSTRAINT `fk_lista_instancia` FOREIGN KEY (`id_instancia`) REFERENCES `instancia_electoral` (`id_instancia`) ON DELETE CASCADE;

--
-- Filtros para la tabla `padron_electoral`
--
ALTER TABLE `padron_electoral`
  ADD CONSTRAINT `fk_padron_alumno` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`),
  ADD CONSTRAINT `fk_padron_instancia` FOREIGN KEY (`id_instancia`) REFERENCES `instancia_electoral` (`id_instancia`) ON DELETE CASCADE;

--
-- Filtros para la tabla `votos`
--
ALTER TABLE `votos`
  ADD CONSTRAINT `fk_voto_instancia` FOREIGN KEY (`id_instancia`) REFERENCES `instancia_electoral` (`id_instancia`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_voto_lista` FOREIGN KEY (`id_lista`) REFERENCES `lista` (`id_lista`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
