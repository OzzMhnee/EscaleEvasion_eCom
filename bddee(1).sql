-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 07 août 2025 à 04:41
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bddee`
--

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'Maison entière'),
(4, 'Chalet'),
(6, 'Loft'),
(7, 'Tiny house');

-- --------------------------------------------------------

--
-- Structure de la table `contact_message`
--

DROP TABLE IF EXISTS `contact_message`;
CREATE TABLE IF NOT EXISTS `contact_message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `contact_message`
--

INSERT INTO `contact_message` (`id`, `first_name`, `last_name`, `email`, `message`, `product_name`, `product_id`, `created_at`) VALUES
(1, 'Bernie', 'NOEL', 'BernieNoel@BN.fr', 'Test pour voir si mon message est bien enregistré en bdd', NULL, NULL, '2025-08-05 11:45:20');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250731105910', '2025-07-31 10:59:17', 65),
('DoctrineMigrations\\Version20250731111639', '2025-07-31 11:16:45', 8),
('DoctrineMigrations\\Version20250731125205', '2025-07-31 12:52:13', 82),
('DoctrineMigrations\\Version20250731132504', '2025-07-31 13:25:07', 29),
('DoctrineMigrations\\Version20250731133803', '2025-07-31 13:38:08', 20),
('DoctrineMigrations\\Version20250731152003', '2025-07-31 15:52:32', 12),
('DoctrineMigrations\\Version20250801093758', '2025-08-01 09:38:13', 86),
('DoctrineMigrations\\Version20250801094627', '2025-08-01 09:46:30', 14),
('DoctrineMigrations\\Version20250803205312', '2025-08-03 20:53:20', 72),
('DoctrineMigrations\\Version20250803210234', '2025-08-03 21:02:43', 39),
('DoctrineMigrations\\Version20250805112004', '2025-08-05 11:20:17', 30);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `price` int NOT NULL,
  `couchages` int NOT NULL,
  `departement` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surface` int NOT NULL,
  `is_available` tinyint(1) NOT NULL,
  `image2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image5` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_swimming_pool` tinyint(1) NOT NULL,
  `is_bath` tinyint(1) NOT NULL,
  `is_clim` tinyint(1) NOT NULL,
  `is_lave_linge` tinyint(1) NOT NULL,
  `is_seche_linge` tinyint(1) NOT NULL,
  `is_lave_vaisselle` tinyint(1) NOT NULL,
  `is_chauffage` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `product`
--

INSERT INTO `product` (`id`, `name`, `description`, `price`, `couchages`, `departement`, `start_date`, `end_date`, `image_url`, `city`, `surface`, `is_available`, `image2`, `image3`, `image4`, `image5`, `is_swimming_pool`, `is_bath`, `is_clim`, `is_lave_linge`, `is_seche_linge`, `is_lave_vaisselle`, `is_chauffage`) VALUES
(3, 'Mélissa', 'Villa Mélissa – Élégance, calme et lumière dans un cadre d’exception\r\n\r\nOffrez-vous une parenthèse de raffinement au cœur d’un environnement préservé.\r\nNichée dans un parc arboré aux accents méditerranéens, la Villa Éléonore incarne le mariage parfait entre architecture classique, design contemporain et prestations haut de gamme.\r\n\r\nDès l’entrée, la villa vous enveloppe dans une atmosphère chic et chaleureuse, pensée pour des séjours inoubliables.\r\n\r\nElle offre un vaste salon lumineux aux volumes généreux, ouvert sur la terrasse et la piscine, une cuisine d’exception entièrement équipée avec îlot central et électroménager haut de gamme, une suite parentale avec salle de bain privée et accès direct au jardin, 2 chambres supplémentaires au style sobre et élégant, une salle à manger intérieure lumineuse et confortable, plusieurs espaces de détente intérieurs, baignés de lumière grâce aux grandes baies vitrées en arc.\r\n\r\nUne façade majestueuse à colonnades et volets d’inspiration provençale, une piscine privative en pierre naturelle avec mobilier de détente, éclairage d’ambiance et jardin méditerranéen parfaitement entretenu, une terrasse élégante pour les petits-déjeuners face au lever du soleil ou les apéritifs à la lueur du soir, offrez-vous un moment bonheur chez Mélissa !', 500, 6, 'Department', '2025-08-01 10:00:00', '2025-08-31 11:00:00', '688b9a2baefbd.jpg', 'City', 130, 1, '688b9a2baf28a.jpg', '688b9a2baf4af.jpg', '688b9a2baf6a5.jpg', '688b9a2baf8a9.jpg', 1, 1, 1, 1, 1, 1, 1),
(4, 'Sabrina', 'Venez séjourner dans cette maison confortable et champêtre. Située dans un petit hameau, vous serez proche de toutes les commodités en moins de 5 minutes en voiture, à 20 minutes de la plage, tout en étant dans un endroit paisible et calme.', 230, 6, 'département', '2025-08-01 10:00:00', '2025-08-10 10:00:00', '688bcbe30d53c.jpg', 'ville', 145, 0, '688bcbe30ddba.jpg', '688bcbe30e1f6.jpg', '688bcbe30e889.jpg', '688bcbe30eea9.jpg', 1, 0, 1, 1, 0, 1, 1),
(5, 'Jérémie', 'Maison Jérémie - contemporaine & chaleureuse avec jardin – Idéale en famille\r\n\r\nBienvenue dans cette splendide villa contemporaine baignée de lumière, nichée au cœur d’un environnement verdoyant, calme et parfaitement entretenu. Avec son style épuré, sa décoration naturelle et ses prestations haut de gamme, cette maison est le lieu idéal pour un séjour reposant en famille ou entre amis.\r\n\r\n🛏️ 3 chambres confortables\r\n-    Chambre parentale avec lit king size, doubles baies vitrées donnant sur le jardin, et literie haut de gamme.\r\n-    Chambre enfants ludique et paisible avec lit cabane, peluches, mobilier en bois naturel et accès direct au jardin.\r\n-    Chambre d’amis ou second espace nuit cosy, décoré dans des tons doux et apaisants.\r\n\r\n🛋️ Salon & salle à manger\r\n\r\n -   Grand salon lumineux avec baie vitrée, canapé moelleux, éclairage d’ambiance et accès direct à la terrasse.\r\n-    Atmosphère cocooning idéale pour les soirées lecture, jeux de société ou films en famille.\r\n-    🍽️ Cuisine design toute équipée, ouverte moderne avec îlot central, équipements dernier cri, vaisselle complète et nombreux rangements.\r\n-    Accès direct à la terrasse pour vos petits-déjeuners au soleil ou apéritifs au coucher du soleil.\r\n\r\n🌅 Terrasse & jardin\r\n\r\n -   Grande terrasse aménagée avec salon extérieur design.\r\n-    Jardin paysagé parfaitement entretenu, propice à la détente, aux jeux d’enfants ou aux repas en plein air.', 130, 5, 'département', '2025-07-17 10:00:00', '2026-09-20 10:00:00', '688bcfba2e3b6.jpg', 'city', 130, 1, '688bcfba2ec16.jpg', '688bcfba2f293.jpg', '688bcfba2f773.jpg', '688bcfba2f9e6.jpg', 0, 0, 1, 1, 0, 0, 1),
(6, 'Aziza', 'Tiny House \"Aziza - Soleil & Voiles\" – Évasion Nature en Bord de Mer\r\n\r\nBienvenue dans notre tiny house de charme, nichée au cœur d’une pinède préservée et ouverte sur l’horizon marin. À quelques pas de la plage, ce cocon de bois vous invite à une expérience unique entre confort moderne et nature sauvage.\r\n\r\nÀ l’intérieur, vous trouverez une chambre cosy avec lit double et vue imprenable sur l’océan, un salon lumineux avec grandes baies vitrées et coin lecture, une salle à manger en bois clair, baignée de lumière naturelle, une cuisine compacte toute équipée (plaques, vaisselle, bouilloire, cafetière), la climatisation réversible pour un confort en toute saison.\r\n\r\nProfitez de deux terrasses panoramiques :\r\n-    La terrasse principale en bois, ouverte sur la plage, idéale pour vos petits déjeuners face aux voiliers\r\n-    Le rooftop intimiste avec fauteuils lounge, parfait pour l’apéro au coucher du soleil.\r\n\r\nLe soir venu, laissez-vous bercer par la douce lueur des guirlandes lumineuses, autour du brasero pour une veillée inoubliable.', 95, 2, 'Departement', '2026-03-25 10:10:00', '2026-09-25 10:00:00', '688bd78def62e.jpg', 'City', 25, 1, '688bd78def93c.jpg', '688bd78defb96.jpg', '688bd78defdc4.jpg', '688bd78deffbb.jpg', 0, 0, 1, 0, 0, 0, 1),
(7, 'Maxime', 'Confort & Élégance entre Forêt et Piscine Privée.\r\n\r\nBienvenue à la Villa Maxime, une élégante maison de plain-pied nichée au cœur d’un cadre verdoyant et apaisant. À seulement quelques minutes des plages ou des sentiers forestiers, cette villa moderne offre un équilibre parfait entre nature, confort et raffinement.\r\n\r\nCette charmante villa d’architecture basque revisitée se compose d’un salon lumineux ouvert sur la terrasse, d’une cuisine entièrement équipée, de 2 chambres confortables, de 2 salles de bain modernes ainsi que d’un garage privé et d’une allée pavée pour plusieurs véhicules.\r\n\r\nAu cœur du jardin : une magnifique piscine privative, entourée de transats et de végétation luxuriante. Calme absolu garanti.', 70, 4, 'département', '2025-08-30 10:00:00', '2027-09-30 10:00:00', '688be20b79dbd.jpg', 'City', 80, 1, '688be20b7a0e2.jpg', '688be20b7a9db.jpg', '688be20b7ac14.jpg', NULL, 1, 1, 1, 1, 0, 1, 1),
(8, 'Patience', 'Villa Patience – Sérénité, espace et nature en parfaite harmonie\r\n\r\nBienvenue à la Villa Patience, une splendide maison familiale aux allures méditerranéennes, nichée dans un écrin de verdure. Idéale pour des vacances ressourçantes, cette propriété allie élégance, confort moderne et atmosphère conviviale à quelques minutes seulement des plages ou des villages typiques de la région.\r\n\r\nSpacieuse et baignée de lumière naturelle, la Villa Patience vous accueille avec un vaste salon ouvert sur le jardin, une cuisine moderne et conviviale avec îlot central, une salle à manger lumineuse au style contemporain, 4 chambres élégantes, dont une chambre d’enfant pensée comme un univers ludique, 2 salles de bains avec douche à l’italienne, une belle entrée paysagée avec stationnement privé ainsi que sa piscine extérieure.', 135, 5, 'Department', '2027-09-25 20:00:00', '2036-07-30 00:00:00', '688be60333625.jpg', 'City', 135, 1, '688be6033392c.jpg', '688be60333b5c.jpg', '688be60333eff.jpg', '688be60334153.jpg', 1, 0, 1, 1, 1, 1, 1),
(9, 'Arnaud', 'Maison d’architecte avec piscine, au calme – 4 chambres, 8 couchages\r\n\r\nBienvenue dans cette somptueuse villa contemporaine nichée au cœur de la nature, à l’abri des regards. Pensée pour le confort et le bien-être, cette maison allie design, lumière naturelle et espaces ouverts, idéale pour des vacances en famille ou entre amis.\r\n\r\n🛏 Chambres & Couchages\r\n-    Suite parentale avec accès direct à la terrasse, lit queen size, décoration moderne et vue dégagée sur le jardin.\r\n-    Chambre enfants sous combles, pensée comme un cocon poétique avec lit simple, tapis moelleux, jeux et ambiance de danseuse.\r\n-    Chambre double décorée avec goût, literie haut de gamme, grande baie vitrée donnant sur la piscine.\r\n-    Chambre d’appoint avec rangement intégré, parfaite pour les ados ou un couple d’amis.\r\n\r\n🍽 Cuisine & Salle à manger\r\nUne cuisine ouverte ultra contemporaine, équipée d’un îlot central sculptural en bois massif, électroménager haut de gamme, parfaite pour cuisiner et partager. Table à manger 8 personnes dans un espace baigné de lumière.\r\n\r\n🛋 Salon & Convivialité\r\nGrand salon avec canapé d’angle confortable, coin lecture, accès direct à la terrasse. Décoration épurée et élégante, éclairage d’ambiance, grande bibliothèque intégrée.\r\n\r\n🌞 Extérieur & Piscine\r\nTerrasse aménagée, transats, mobilier d’extérieur. Piscine privative au sel, sécurisée, avec jardin paysager. Le lieu parfait pour profiter du soleil en toute tranquillité.', 250, 8, 'département', '2025-08-01 10:00:00', '2030-08-01 20:30:00', '688bebac1097b.jpg', 'city', 175, 1, '688bebac10c43.jpg', '688bebac112f1.jpg', '688bebac11531.jpg', '688bebac1173a.jpg', 1, 1, 1, 1, 1, 1, 1),
(10, 'Amine', 'Villa Serena – Évasion Élégante en Provence\r\n\r\nNichée dans un écrin de verdure, la Villa Serena est une somptueuse demeure contemporaine pensée pour le confort, le partage et le raffinement. Idéale pour des vacances en famille ou entre amis, cette maison d’architecte allie matériaux nobles, design chaleureux et prestations haut de gamme.\r\n\r\n🏡 Une maison pensée pour vivre dedans comme dehors :\r\nDès l’entrée, vous serez saisis par la lumière naturelle qui baigne le vaste espace de vie. Le salon s’ouvre généreusement sur une terrasse verdoyante et un jardin paysager avec piscine à débordement. La cuisine américaine moderne, ouverte sur une grande table conviviale, invite à la gastronomie et aux moments partagés.\r\n\r\n🛏 Chambres intimistes et cocons de repos\r\nLes chambres sont toutes décorées avec soin. La suite parentale vous accueille dans des tons doux et naturels, avec un accès direct à l’extérieur. Chaque chambre est équipée de literie haut de gamme et pensée pour votre sérénité.\r\n\r\n🌙 Une chambre d’enfant ludique et poétique\r\nPensée pour éveiller l’imaginaire des plus jeunes, la chambre enfant offre un univers ludique, doux et sécurisé. Mobilier boisé, jeux d’éveil, décor mural enchanteur : tout a été pensé pour leur confort et leur autonomie.\r\n\r\n💻 Un bureau élégant pour allier travail et détente\r\nUn espace de travail lumineux et inspirant est à disposition, pour télétravailler au calme si besoin, ou simplement s’accorder un moment de lecture dans un décor raffiné.\r\n\r\n🌿 Extérieurs enchanteurs et jardin méditerranéen\r\nLa villa s’intègre parfaitement dans le relief naturel de la colline. Vous profiterez d’une vue dégagée sur les pins parasols et d’une tranquillité absolue. La piscine, les transats et l’aire de jeux extérieure promettent des après-midis inoubliables.', 530, 8, 'Departement', '0202-02-20 02:00:00', '2222-02-20 22:02:00', '688beed02628d.jpg', 'City', 142, 1, '688beed026aa2.jpg', '688beed026f3f.jpg', '688beed027425.jpg', '688beed0278d3.jpg', 1, 0, 1, 1, 1, 1, 1),
(11, 'Mounir', '\"Mounir\" Maison d’Architecte avec Jardin – Élégance & Calme à la Campagne\r\n\r\nPlongez dans un cocon de confort, de lumière et de design dans cette magnifique maison contemporaine entourée de verdure. Située dans un environnement paisible, cette propriété vous accueille pour un séjour haut de gamme, en couple ou en famille.\r\n\r\n🏡 Extérieur charmant et verdoyant\r\nDès votre arrivée, vous serez séduit par la façade au style néo-basque revisitée, avec ses volets verts, ses lignes nettes et son jardin paysagé soigneusement entretenu. Une allée élégante vous mène à l’entrée principale et au garage. La végétation luxuriante autour de la maison invite à la détente.\r\n\r\n🍽️ Cuisine design avec îlot central\r\nLa cuisine est un espace d’exception, mêlant bois naturel et résine claire dans un style graphique raffiné. Entièrement équipée, elle dispose d’un îlot central convivial pour partager les petits-déjeuners ou préparer les repas en toute sérénité. Une belle lumière naturelle baigne l’ensemble.\r\n\r\n🛋️ Espaces de vie lumineux & décorés avec soin\r\nLe salon et la salle à manger vous offrent une atmosphère cosy et contemporaine. Chaises design, suspensions sculpturales, œuvres murales et objets déco soigneusement choisis créent une ambiance à la fois chaleureuse et sophistiquée. De grandes baies vitrées donnent accès à la terrasse et laissent entrer la lumière tout au long de la journée.\r\n\r\n🛏️ Chambre principale avec vue sur le jardin\r\nLa chambre parentale, douce et reposante, propose un lit double haut de gamme, une déco minimaliste et élégante, et un accès direct au jardin pour des réveils en pleine nature.\r\n\r\n🌿 Un jardin pour se ressourcer\r\nEntouré d’arbres et de haies bien taillées, le jardin privatif est idéal pour un bain de soleil, un apéritif en terrasse ou une pause lecture. Un vélo à disposition vous permettra de découvrir les alentours.', 111, 5, 'département', '2222-02-20 22:02:00', '4444-02-22 04:22:00', '688bf0b778ca9.jpg', 'City', 95, 1, '688bf0b77937b.jpg', '688bf0b77a065.jpg', '688bf0b77a5d3.jpg', '688bf0b77abfe.jpg', 1, 1, 0, 1, 1, 1, 1),
(12, 'Karim', 'Villa contemporaine haut de gamme avec jardin, piscine et décoration soignée\r\n\r\nBienvenue dans cette villa d’architecte lumineuse et chaleureuse, située dans un environnement résidentiel calme, à quelques minutes des plages et des commodités.\r\n\r\n🌅 Extérieur & jardin\r\nDès l’arrivée, vous serez charmé par la façade élégante en pierre naturelle et les volumes modernes de la maison. Le jardin paysager parfaitement entretenu, aux courbes douces et végétation méditerranéenne, entoure la propriété. Une vaste terrasse en bois accueille un salon de jardin design avec fauteuil suspendu, poufs et bains de soleil, parfaits pour se détendre après une baignade dans la piscine.\r\n\r\n🛋️ Salon & espace de vie\r\nL’intérieur s’ouvre sur un grand séjour baigné de lumière grâce aux baies vitrées à galandage. Les poutres apparentes et les matériaux nobles (bois, pierre, béton ciré) confèrent à l’ensemble une atmosphère à la fois conviviale et raffinée. Le salon et la salle à manger s’articulent autour d’un mobilier contemporain, d’objets de décoration uniques et d’une TV écran plat.\r\n\r\n🍽️ Cuisine ouverte\r\nLa cuisine américaine entièrement équipée (four, plaque, réfrigérateur, lave-vaisselle, cafetière…) permet de cuisiner en toute convivialité tout en profitant des échanges avec vos proches.\r\n\r\n🛏️ Chambres\r\nLes chambres offrent de beaux volumes, une literie de qualité hôtelière et une ambiance apaisante. La suite parentale dispose de son propre dressing et d’un accès direct à la terrasse.\r\n\r\n🛁 Salle de bain\r\nLa salle de bain mêle charme et modernité, avec une baignoire, des carreaux graphiques, un grand miroir et un éclairage doux. Des serviettes moelleuses et du linge de maison sont mis à disposition.', 210, 6, 'département', '2030-07-25 20:30:00', '2040-08-30 20:40:00', '688bfa0f3e064.jpg', 'City', 142, 0, '688bfa0f3e9d5.jpg', '688bfa0f3f04b.jpg', '688bfa0f3f5d2.jpg', '688bfa0f3fa96.jpg', 1, 1, 1, 1, 1, 1, 1),
(13, 'Charles', 'Collection Résidentielle Haut de Gamme – Intérieurs & Extérieurs\r\n\r\n🏡 Architecture contemporaine & élégance naturelle\r\nChaque visuel incarne une vision raffinée de l’habitat moderne, alliant lignes épurées, matériaux nobles et intégration paysagère harmonieuse. Toitures à deux pans, bardages bois, murs en pierres claires et grandes baies vitrées confèrent à ces maisons un caractère à la fois chaleureux et résolument contemporain.\r\nExterieurs\r\n    Maisons avec piscine & terrasses en bois : ambiance de vacances à l’année, parfaite pour les soirées d’été et les instants de détente en famille. Les lumières d’ambiance et les végétaux méditerranéens renforcent le charme du lieu.\r\n    Entrée soignée & paysagisme maîtrisé : allées pavées, massifs de graminées et éclairage doux offrent une première impression digne des plus belles villas contemporaines.\r\n\r\nIntérieurs\r\n    Salon & salle à manger : espaces ouverts baignés de lumière naturelle, avec cheminée design et mobilier minimaliste, parfait équilibre entre confort et sophistication.\r\n    Cuisine moderne avec ilot central : design épuré, matériaux sobres et fonctionnels, liaison directe avec l’extérieur pour une vie dedans-dehors fluide.\r\n    Chambres parentales & suites : ambiance douce et cocooning, décoration sobre et chaleureuse, avec une touche de poésie (cf. le tableau au-dessus du lit).\r\n\r\nStyle visuel et atmosphère\r\nLes rendus hyperréalistes accentuent la qualité perçue des volumes, matières et lumières naturelles, dans un esprit digne des meilleures campagnes de promotion immobilière ou d’architectes. Chaque image semble issue d’un shooting réel en conditions idéales, parfaite pour séduire un public exigeant.', 230, 5, 'département', '2022-12-25 01:00:00', '2222-12-29 22:22:00', '688bfed027d25.jpg', 'City', 130, 1, '688bfed028532.jpg', '688bfed028a14.jpg', '688bfed028c61.jpg', '688bfed028e56.jpg', 1, 1, 0, 1, 1, 1, 1),
(14, 'Saria', '🏡 Villa contemporaine avec piscine – calme, luxe et design en pleine nature\r\nBienvenue dans cette villa d\'architecte haut de gamme, nichée dans un environnement verdoyant à l\'abri des regards. Avec ses grandes baies vitrées, son intérieur baigné de lumière et sa piscine privée, cette maison allie élégance, confort et sérénité.\r\n\r\n🛏️ 4 chambres spacieuses, toutes ouvertes sur l’extérieur, avec literie haut de gamme et décoration soignée.\r\n🛁 3 salles de bains modernes, dont une suite parentale avec vue sur la piscine.\r\n🍽️ Cuisine ouverte design, entièrement équipée, parfaite pour les repas en famille ou entre amis.\r\n🛋️ Salon lumineux et cosy, mobilier épuré, matériaux nobles et accès direct à la terrasse.\r\n🌴 Jardin paysager avec oliviers, palmiers et coin détente.\r\n☀️ Grande terrasse ensoleillée, transats, pergola et vue dégagée.\r\n🏊‍♂️ Piscine privée au sel, sécurisée et sans vis-à-vis.\r\n\r\n📍 Située à quelques minutes des plages / du centre-ville / d’un golf (à adapter selon localisation réelle).\r\n📶 Wi-Fi haut débit, climatisation, TV connectée, parking privé.\r\n\r\n✨ Le mot du propriétaire :\r\n« Ici, chaque détail a été pensé pour vous offrir une parenthèse de calme et d’esthétique. Un lieu idéal pour se ressourcer, télétravailler ou simplement profiter. »', 560, 8, 'département', '2026-05-02 22:22:00', '2222-02-05 22:22:00', '688c64948af80.jpg', 'City', 235, 1, '688c64948b76e.jpg', '688c64948c22e.jpg', '688c64948c49e.jpg', '688c64948c6ef.jpg', 1, 1, 1, 1, 1, 1, 0),
(15, 'Julen', NULL, 250, 6, 'département', '2025-05-25 10:00:00', '2030-05-25 03:00:00', '688c87d798448.jpg', 'City', 135, 1, '688c87d798758.jpg', '688c87d7989c8.jpg', '688c87d798c0e.jpg', '688c87d798e58.jpg', 1, 1, 1, 1, 0, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `product_sub_category`
--

DROP TABLE IF EXISTS `product_sub_category`;
CREATE TABLE IF NOT EXISTS `product_sub_category` (
  `product_id` int NOT NULL,
  `sub_category_id` int NOT NULL,
  PRIMARY KEY (`product_id`,`sub_category_id`),
  KEY `IDX_3147D5F34584665A` (`product_id`),
  KEY `IDX_3147D5F3F7BFE87C` (`sub_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `product_sub_category`
--

INSERT INTO `product_sub_category` (`product_id`, `sub_category_id`) VALUES
(3, 1),
(3, 2),
(3, 5),
(4, 1),
(4, 4),
(5, 2),
(5, 4),
(6, 6),
(6, 7),
(7, 3),
(7, 5),
(8, 2),
(8, 3),
(9, 3),
(9, 5),
(10, 2),
(10, 5),
(11, 3),
(11, 5),
(12, 1),
(12, 3),
(13, 2),
(13, 5),
(14, 2),
(14, 3),
(15, 3),
(15, 5);

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
CREATE TABLE IF NOT EXISTS `reservation` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_42C849554584665A` (`product_id`),
  KEY `IDX_42C84955A76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`id`, `product_id`, `start_date`, `end_date`, `status`, `created_at`, `user_id`) VALUES
(1, 6, '2025-08-04 00:00:00', '2025-08-31 00:00:00', 'annulée', '2025-08-03 21:05:01', 1),
(2, 6, '2025-09-09 00:00:00', '2025-08-29 00:00:00', 'annulée', '2025-08-03 22:59:02', 1),
(3, 6, '2025-10-01 00:00:00', '2025-10-31 00:00:00', 'confirmée', '2025-08-05 07:45:01', 1),
(4, 8, '2025-08-25 00:00:00', '2025-09-05 00:00:00', 'annulée', '2025-08-05 07:46:55', 1),
(5, 6, '2026-01-01 00:00:00', '2026-01-31 00:00:00', 'annulée', '2025-08-05 12:09:26', 1),
(6, 5, '2025-08-06 00:00:00', '2025-08-07 00:00:00', 'en attente', '2025-08-06 08:37:10', 1),
(7, 6, '2025-09-20 00:00:00', '2025-09-25 00:00:00', 'en attente', '2025-08-06 09:19:44', 1);

-- --------------------------------------------------------

--
-- Structure de la table `sub_category`
--

DROP TABLE IF EXISTS `sub_category`;
CREATE TABLE IF NOT EXISTS `sub_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BCE3F79812469DE2` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sub_category`
--

INSERT INTO `sub_category` (`id`, `category_id`, `name`) VALUES
(1, 1, 'Bord de mer'),
(2, 1, 'Montagne'),
(3, 1, 'Campagne'),
(4, 1, 'Ville'),
(5, 1, 'Forêt'),
(6, 7, 'Bord de mer'),
(7, 7, 'Forêt');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `first_name`, `last_name`) VALUES
(1, 'BernieNoel@BN.fr', '[\"ROLE_ADMIN\", \"ROLE_EDITOR\", \"ROLE_USER\"]', '$2y$13$XxaJPw2kJd8sdncGUDIuy.Qv6uAWV7sbANTa4uYQafVKY5pcqlNGe', 'Bernie', 'NOEL'),
(2, 'BertrandC@mail.fr', '[\"ROLE_EDITOR\"]', '$2y$13$zDQrckRXGZlxY7/F7C1DOulknZY5ZjEtZy8DvdiHgW6npr3YQq7WC', 'Bertrand', 'CANTAT'),
(3, 'JLM@mail.fr', '[\"ROLE_ADMIN\", \"ADMIN_EDITOR\", \"ROLE_USER\"]', '$2y$13$XR3tMkc.wXB9255NMB3gYOtW.QSOW9p10zAG9XNMD8eqLNnqCUeWS', 'Jeremie', 'LM'),
(4, 'BG@mail.fr', '[]', '$2y$13$D5DtOTCDit5iARhe1tTOieJVGOa6CvE957qsZ1MUHhyIIu8uUCld.', 'Bill', 'Gates'),
(5, 'ADupontel@mail.fr', '[]', '$2y$13$Y0qNkoeRV6mStoaerOQEEORZzS33GbjRS58JN4AKMqv.je.Mz.pOW', 'Albert', 'DUPONTEL'),
(6, 'Rocket@GDLG.fr', '[]', '$2y$13$jZBkRb4WY/yxVlYOJePs5uNvnyeSOMS/33NsbnFaZLMhd9HlTCPJi', 'Rocket', 'GDLG');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `product_sub_category`
--
ALTER TABLE `product_sub_category`
  ADD CONSTRAINT `FK_3147D5F34584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_3147D5F3F7BFE87C` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `FK_42C849554584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `FK_42C84955A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `sub_category`
--
ALTER TABLE `sub_category`
  ADD CONSTRAINT `FK_BCE3F79812469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
