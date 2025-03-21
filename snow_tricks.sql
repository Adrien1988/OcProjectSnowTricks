-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : ven. 21 mars 2025 à 14:35
-- Version du serveur : 8.0.30
-- Version de PHP : 8.1.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `snow_tricks`
--

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

CREATE TABLE `comment` (
  `id` int NOT NULL,
  `author_id` int NOT NULL,
  `figure_id` int NOT NULL,
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`id`, `author_id`, `figure_id`, `content`, `created_at`) VALUES
(351, 38, 89, 'Je préfère les rotations désaxées, mais j\'avoue que ça en jette.', '2025-03-06 14:28:24'),
(352, 38, 89, 'Trop stylé, j\'ajoute ça à ma liste de figures à apprendre.', '2025-03-17 14:28:24'),
(353, 38, 89, 'Pas mal du tout, tu gères vraiment.', '2025-02-28 14:28:24'),
(354, 39, 89, 'Je préfère les rotations désaxées, mais j\'avoue que ça en jette.', '2025-02-21 14:28:24'),
(355, 39, 89, 'Je préfère les rotations désaxées, mais j\'avoue que ça en jette.', '2025-02-28 14:28:24'),
(356, 40, 89, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-03-03 14:28:24'),
(357, 40, 89, 'Ça a l\'air simple en théorie, mais en pratique c\'est chaud !', '2025-03-04 14:28:24'),
(358, 41, 89, 'Je trouve ça difficile à exécuter, mais ça rend vraiment bien.', '2025-03-19 14:28:24'),
(359, 41, 89, 'Wow, c\'est super impressionnant !', '2025-03-08 14:28:24'),
(360, 42, 89, 'Ça a l\'air simple en théorie, mais en pratique c\'est chaud !', '2025-03-19 14:28:24'),
(361, 42, 89, 'Merci pour le partage, je ne connaissais pas cette variante.', '2025-02-19 14:28:24'),
(362, 42, 89, 'J\'adore cette figure, je vais essayer de la reproduire ce week-end !', '2025-03-03 14:28:24'),
(363, 43, 89, 'Est-ce que tu conseillerais un stance plus large pour ce trick ?', '2025-03-21 14:28:24'),
(364, 43, 89, 'Wow, c\'est super impressionnant !', '2025-03-09 14:28:24'),
(365, 43, 89, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-03-12 14:28:24'),
(366, 38, 90, 'Merci pour le partage, je ne connaissais pas cette variante.', '2025-02-21 14:28:24'),
(367, 39, 90, 'Pas mal du tout, tu gères vraiment.', '2025-03-16 14:28:24'),
(368, 39, 90, 'Est-ce que tu conseillerais un stance plus large pour ce trick ?', '2025-03-02 14:28:24'),
(369, 39, 90, 'Félicitations, c\'est un beau trick !', '2025-03-15 14:28:24'),
(370, 40, 90, 'Est-ce que tu conseillerais un stance plus large pour ce trick ?', '2025-03-05 14:28:24'),
(371, 41, 90, 'J\'adore cette figure, je vais essayer de la reproduire ce week-end !', '2025-03-18 14:28:24'),
(372, 41, 90, 'Merci pour le partage, je ne connaissais pas cette variante.', '2025-03-01 14:28:24'),
(373, 42, 90, 'Est-ce que tu conseillerais un stance plus large pour ce trick ?', '2025-03-06 14:28:24'),
(374, 42, 90, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-03-06 14:28:24'),
(375, 42, 90, 'Magnifique, j\'aime le style old school.', '2025-03-20 14:28:24'),
(376, 43, 90, 'Pas mal du tout, tu gères vraiment.', '2025-03-02 14:28:24'),
(377, 43, 90, 'Wow, c\'est super impressionnant !', '2025-03-16 14:28:24'),
(378, 38, 91, 'Magnifique, j\'aime le style old school.', '2025-02-20 14:28:24'),
(379, 38, 91, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-02-27 14:28:24'),
(380, 38, 91, 'J\'adore cette figure, je vais essayer de la reproduire ce week-end !', '2025-03-11 14:28:24'),
(381, 39, 91, 'Toujours un plaisir de voir de nouvelles figures !', '2025-02-19 14:28:24'),
(382, 39, 91, 'Je trouve ça difficile à exécuter, mais ça rend vraiment bien.', '2025-03-18 14:28:24'),
(383, 40, 91, 'Magnifique, j\'aime le style old school.', '2025-03-13 14:28:24'),
(384, 40, 91, 'Toujours un plaisir de voir de nouvelles figures !', '2025-03-13 14:28:24'),
(385, 41, 91, 'Toujours un plaisir de voir de nouvelles figures !', '2025-03-21 14:28:24'),
(386, 42, 91, 'Je préfère les rotations désaxées, mais j\'avoue que ça en jette.', '2025-02-19 14:28:24'),
(387, 42, 91, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-03-04 14:28:24'),
(388, 43, 91, 'Félicitations, c\'est un beau trick !', '2025-03-08 14:28:24'),
(389, 43, 91, 'Je trouve ça difficile à exécuter, mais ça rend vraiment bien.', '2025-02-22 14:28:24'),
(390, 38, 92, 'Magnifique, j\'aime le style old school.', '2025-03-16 14:28:24'),
(391, 38, 92, 'Est-ce que tu conseillerais un stance plus large pour ce trick ?', '2025-03-05 14:28:24'),
(392, 38, 92, 'Est-ce que tu conseillerais un stance plus large pour ce trick ?', '2025-03-09 14:28:24'),
(393, 39, 92, 'Wow, c\'est super impressionnant !', '2025-02-26 14:28:24'),
(394, 39, 92, 'Merci pour le partage, je ne connaissais pas cette variante.', '2025-03-13 14:28:24'),
(395, 39, 92, 'Est-ce que tu conseillerais un stance plus large pour ce trick ?', '2025-03-05 14:28:24'),
(396, 40, 92, 'J\'adore cette figure, je vais essayer de la reproduire ce week-end !', '2025-03-17 14:28:24'),
(397, 40, 92, 'Wow, c\'est super impressionnant !', '2025-03-10 14:28:24'),
(398, 41, 92, 'Magnifique, j\'aime le style old school.', '2025-02-21 14:28:24'),
(399, 41, 92, 'Pas mal du tout, tu gères vraiment.', '2025-02-27 14:28:24'),
(400, 41, 92, 'Pas mal du tout, tu gères vraiment.', '2025-03-06 14:28:24'),
(401, 42, 92, 'Toujours un plaisir de voir de nouvelles figures !', '2025-03-11 14:28:24'),
(402, 42, 92, 'Est-ce que tu conseillerais un stance plus large pour ce trick ?', '2025-03-09 14:28:24'),
(403, 43, 92, 'J\'adore cette figure, je vais essayer de la reproduire ce week-end !', '2025-02-26 14:28:24'),
(404, 38, 93, 'J\'adore cette figure, je vais essayer de la reproduire ce week-end !', '2025-03-08 14:28:24'),
(405, 38, 93, 'Pas mal du tout, tu gères vraiment.', '2025-02-27 14:28:24'),
(406, 39, 93, 'Toujours un plaisir de voir de nouvelles figures !', '2025-02-19 14:28:24'),
(407, 40, 93, 'Félicitations, c\'est un beau trick !', '2025-03-08 14:28:24'),
(408, 41, 93, 'Ça a l\'air simple en théorie, mais en pratique c\'est chaud !', '2025-03-02 14:28:24'),
(409, 42, 93, 'Wow, c\'est super impressionnant !', '2025-02-26 14:28:24'),
(410, 42, 93, 'J\'adore cette figure, je vais essayer de la reproduire ce week-end !', '2025-03-13 14:28:24'),
(411, 43, 93, 'J\'adore cette figure, je vais essayer de la reproduire ce week-end !', '2025-03-10 14:28:24'),
(412, 43, 93, 'Toujours un plaisir de voir de nouvelles figures !', '2025-03-15 14:28:24'),
(413, 43, 93, 'Pas mal du tout, tu gères vraiment.', '2025-03-09 14:28:24'),
(414, 38, 94, 'Toujours un plaisir de voir de nouvelles figures !', '2025-03-10 14:28:24'),
(415, 38, 94, 'Trop stylé, j\'ajoute ça à ma liste de figures à apprendre.', '2025-03-03 14:28:24'),
(416, 38, 94, 'Je trouve ça difficile à exécuter, mais ça rend vraiment bien.', '2025-03-08 14:28:24'),
(417, 39, 94, 'Magnifique, j\'aime le style old school.', '2025-03-02 14:28:24'),
(418, 39, 94, 'Merci pour le partage, je ne connaissais pas cette variante.', '2025-03-15 14:28:24'),
(419, 39, 94, 'Félicitations, c\'est un beau trick !', '2025-03-05 14:28:24'),
(420, 40, 94, 'Wow, c\'est super impressionnant !', '2025-03-16 14:28:24'),
(421, 40, 94, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-03-11 14:28:24'),
(422, 40, 94, 'Est-ce que tu conseillerais un stance plus large pour ce trick ?', '2025-03-08 14:28:24'),
(423, 41, 94, 'Trop stylé, j\'ajoute ça à ma liste de figures à apprendre.', '2025-02-26 14:28:24'),
(424, 41, 94, 'Je préfère les rotations désaxées, mais j\'avoue que ça en jette.', '2025-03-20 14:28:24'),
(425, 42, 94, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-03-18 14:28:24'),
(426, 43, 94, 'Je trouve ça difficile à exécuter, mais ça rend vraiment bien.', '2025-03-01 14:28:24'),
(427, 43, 94, 'Je préfère les rotations désaxées, mais j\'avoue que ça en jette.', '2025-03-07 14:28:24'),
(428, 38, 95, 'Wow, c\'est super impressionnant !', '2025-03-17 14:28:24'),
(429, 38, 95, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-02-22 14:28:24'),
(430, 38, 95, 'Wow, c\'est super impressionnant !', '2025-02-20 14:28:24'),
(431, 39, 95, 'Est-ce que tu conseillerais un stance plus large pour ce trick ?', '2025-03-02 14:28:24'),
(432, 40, 95, 'Trop stylé, j\'ajoute ça à ma liste de figures à apprendre.', '2025-03-02 14:28:24'),
(433, 40, 95, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-02-28 14:28:24'),
(434, 40, 95, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-02-19 14:28:24'),
(435, 41, 95, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-03-10 14:28:24'),
(436, 41, 95, 'Ça a l\'air simple en théorie, mais en pratique c\'est chaud !', '2025-02-26 14:28:24'),
(437, 41, 95, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-03-13 14:28:24'),
(438, 42, 95, 'Merci pour le partage, je ne connaissais pas cette variante.', '2025-03-21 14:28:24'),
(439, 42, 95, 'Magnifique, j\'aime le style old school.', '2025-03-04 14:28:24'),
(440, 42, 95, 'Merci pour le partage, je ne connaissais pas cette variante.', '2025-03-08 14:28:24'),
(441, 43, 95, 'Je trouve ça difficile à exécuter, mais ça rend vraiment bien.', '2025-03-12 14:28:24'),
(442, 38, 96, 'Je trouve ça difficile à exécuter, mais ça rend vraiment bien.', '2025-03-21 14:28:24'),
(443, 39, 96, 'Trop stylé, j\'ajoute ça à ma liste de figures à apprendre.', '2025-02-22 14:28:24'),
(444, 39, 96, 'Wow, c\'est super impressionnant !', '2025-02-28 14:28:24'),
(445, 39, 96, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-03-08 14:28:24'),
(446, 40, 96, 'J\'adore cette figure, je vais essayer de la reproduire ce week-end !', '2025-02-24 14:28:24'),
(447, 40, 96, 'Ça a l\'air simple en théorie, mais en pratique c\'est chaud !', '2025-02-22 14:28:24'),
(448, 40, 96, 'J\'adore cette figure, je vais essayer de la reproduire ce week-end !', '2025-03-13 14:28:24'),
(449, 41, 96, 'Trop stylé, j\'ajoute ça à ma liste de figures à apprendre.', '2025-03-01 14:28:24'),
(450, 41, 96, 'Je préfère les rotations désaxées, mais j\'avoue que ça en jette.', '2025-03-14 14:28:24'),
(451, 41, 96, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-03-20 14:28:24'),
(452, 42, 96, 'Merci pour le partage, je ne connaissais pas cette variante.', '2025-02-21 14:28:24'),
(453, 42, 96, 'J\'adore cette figure, je vais essayer de la reproduire ce week-end !', '2025-03-12 14:28:24'),
(454, 43, 96, 'Magnifique, j\'aime le style old school.', '2025-03-13 14:28:24'),
(455, 43, 96, 'Magnifique, j\'aime le style old school.', '2025-02-21 14:28:24'),
(456, 38, 97, 'Je préfère les rotations désaxées, mais j\'avoue que ça en jette.', '2025-03-17 14:28:24'),
(457, 38, 97, 'Toujours un plaisir de voir de nouvelles figures !', '2025-03-19 14:28:24'),
(458, 38, 97, 'J\'adore cette figure, je vais essayer de la reproduire ce week-end !', '2025-03-14 14:28:24'),
(459, 39, 97, 'Ça a l\'air simple en théorie, mais en pratique c\'est chaud !', '2025-03-21 14:28:24'),
(460, 39, 97, 'Toujours un plaisir de voir de nouvelles figures !', '2025-03-21 14:28:24'),
(461, 40, 97, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-03-07 14:28:24'),
(462, 40, 97, 'Je trouve ça difficile à exécuter, mais ça rend vraiment bien.', '2025-02-24 14:28:24'),
(463, 40, 97, 'Wow, c\'est super impressionnant !', '2025-02-20 14:28:24'),
(464, 41, 97, 'J\'adore cette figure, je vais essayer de la reproduire ce week-end !', '2025-03-05 14:28:24'),
(465, 41, 97, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-02-26 14:28:24'),
(466, 42, 97, 'Pas mal du tout, tu gères vraiment.', '2025-02-21 14:28:24'),
(467, 42, 97, 'Pas mal du tout, tu gères vraiment.', '2025-02-25 14:28:24'),
(468, 43, 97, 'Je trouve ça difficile à exécuter, mais ça rend vraiment bien.', '2025-02-21 14:28:24'),
(469, 43, 97, 'Wow, c\'est super impressionnant !', '2025-03-06 14:28:24'),
(470, 43, 97, 'Est-ce que tu conseillerais un stance plus large pour ce trick ?', '2025-02-21 14:28:24'),
(471, 38, 98, 'Magnifique, j\'aime le style old school.', '2025-03-03 14:28:24'),
(472, 38, 98, 'Pas mal du tout, tu gères vraiment.', '2025-03-02 14:28:24'),
(473, 38, 98, 'Je trouve ça difficile à exécuter, mais ça rend vraiment bien.', '2025-03-18 14:28:24'),
(474, 39, 98, 'Ça a l\'air simple en théorie, mais en pratique c\'est chaud !', '2025-02-27 14:28:24'),
(475, 39, 98, 'J\'adore cette figure, je vais essayer de la reproduire ce week-end !', '2025-02-25 14:28:24'),
(476, 39, 98, 'J\'ai encore du mal avec le timing, mais ce tuto m\'aide beaucoup.', '2025-03-01 14:28:24'),
(477, 40, 98, 'Pas mal du tout, tu gères vraiment.', '2025-03-17 14:28:24'),
(478, 40, 98, 'Toujours un plaisir de voir de nouvelles figures !', '2025-03-16 14:28:24'),
(479, 40, 98, 'Magnifique, j\'aime le style old school.', '2025-02-24 14:28:24'),
(480, 41, 98, 'Merci pour le partage, je ne connaissais pas cette variante.', '2025-03-21 14:28:24'),
(481, 42, 98, 'Merci pour le partage, je ne connaissais pas cette variante.', '2025-02-27 14:28:24'),
(482, 42, 98, 'J\'adore cette figure, je vais essayer de la reproduire ce week-end !', '2025-03-13 14:28:24'),
(483, 43, 98, 'Toujours un plaisir de voir de nouvelles figures !', '2025-03-01 14:28:24');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250219123436', '2025-02-19 12:58:27', 101);

-- --------------------------------------------------------

--
-- Structure de la table `figure`
--

CREATE TABLE `figure` (
  `id` int NOT NULL,
  `author_id` int NOT NULL,
  `main_image_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `figure_group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `figure`
--

INSERT INTO `figure` (`id`, `author_id`, `main_image_id`, `name`, `description`, `slug`, `figure_group`, `created_at`, `updated_at`) VALUES
(89, 39, 234, 'Mute', 'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.', 'mute', 'Grabs', '2025-03-21 14:28:23', '2025-03-21 14:28:23'),
(90, 39, 238, 'Indy', 'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.', 'indy', 'Grabs', '2025-03-21 14:28:23', '2025-03-21 14:28:23'),
(91, 43, 242, 'Backflip', 'Rotation verticale en arrière.', 'backflip', 'Flips', '2025-03-21 14:28:23', '2025-03-21 14:28:23'),
(92, 38, 245, 'Frontside 360', 'Rotation horizontale de 360 degrés en frontside.', 'frontside-360', 'Rotations', '2025-03-21 14:28:23', '2025-03-21 14:28:23'),
(93, 40, 248, 'Method Air', 'Old school : saisir la carre backside en fléchissant les jambes, corps tendu.', 'method-air', 'Old School', '2025-03-21 14:28:23', '2025-03-21 14:28:23'),
(94, 40, 249, 'Cork 720', 'Rotation désaxée de deux tours complets agrémentée d’un grab.', 'cork-720', 'Rotations désaxées', '2025-03-21 14:28:23', '2025-03-21 14:28:23'),
(95, 38, 254, 'Nose Slide', 'Slide sur une barre avec l’avant de la planche.', 'nose-slide', 'Slides', '2025-03-21 14:28:24', '2025-03-21 14:28:24'),
(96, 40, 255, 'Tail Grab', 'Saisie de la partie arrière de la planche, avec la main arrière.', 'tail-grab', 'Grabs', '2025-03-21 14:28:24', '2025-03-21 14:28:24'),
(97, 41, 259, 'Truck Driver', 'Saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture).', 'truck-driver', 'Grabs', '2025-03-21 14:28:24', '2025-03-21 14:28:24'),
(98, 39, 263, 'Rocket Air', 'Old school : saisir l’avant de la planche avec les deux mains.', 'rocket-air', 'Old School', '2025-03-21 14:28:24', '2025-03-21 14:28:24');

-- --------------------------------------------------------

--
-- Structure de la table `image`
--

CREATE TABLE `image` (
  `id` int NOT NULL,
  `figure_id` int NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `image`
--

INSERT INTO `image` (`id`, `figure_id`, `url`, `alt_text`, `created_at`) VALUES
(234, 89, 'uploads/figure_67dd77878b3d9_image10.jpg', 'Image aléatoire pour Mute', '2025-03-21 14:28:23'),
(235, 89, 'uploads/figure_67dd778792655_image3.jpg', 'Image aléatoire pour Mute', '2025-03-21 14:28:23'),
(236, 89, 'uploads/figure_67dd7787986e0_image8.jpg', 'Image aléatoire pour Mute', '2025-03-21 14:28:23'),
(237, 90, 'uploads/figure_67dd77879e8ac_image10.jpg', 'Image aléatoire pour Indy', '2025-03-21 14:28:23'),
(238, 90, 'uploads/figure_67dd7787a88e8_image7.jpg', 'Image aléatoire pour Indy', '2025-03-21 14:28:23'),
(239, 90, 'uploads/figure_67dd7787af550_image9.jpg', 'Image aléatoire pour Indy', '2025-03-21 14:28:23'),
(240, 91, 'uploads/figure_67dd7787b6227_image11.jpg', 'Image aléatoire pour Backflip', '2025-03-21 14:28:23'),
(241, 91, 'uploads/figure_67dd7787bd09c_image5.jpg', 'Image aléatoire pour Backflip', '2025-03-21 14:28:23'),
(242, 91, 'uploads/figure_67dd7787c3363_image9.jpg', 'Image aléatoire pour Backflip', '2025-03-21 14:28:23'),
(243, 92, 'uploads/figure_67dd7787c9356_image11.jpg', 'Image aléatoire pour Frontside 360', '2025-03-21 14:28:23'),
(244, 92, 'uploads/figure_67dd7787d00fc_image5.jpg', 'Image aléatoire pour Frontside 360', '2025-03-21 14:28:23'),
(245, 92, 'uploads/figure_67dd7787d5fc4_image8.jpg', 'Image aléatoire pour Frontside 360', '2025-03-21 14:28:23'),
(246, 93, 'uploads/figure_67dd7787dbfb8_image1.jpg', 'Image aléatoire pour Method Air', '2025-03-21 14:28:23'),
(247, 93, 'uploads/figure_67dd7787e21c8_image2.jpg', 'Image aléatoire pour Method Air', '2025-03-21 14:28:23'),
(248, 93, 'uploads/figure_67dd7787e810c_image3.jpg', 'Image aléatoire pour Method Air', '2025-03-21 14:28:23'),
(249, 94, 'uploads/figure_67dd7787ee2ec_image10.jpg', 'Image aléatoire pour Cork 720', '2025-03-21 14:28:23'),
(250, 94, 'uploads/figure_67dd7788010af_image2.jpg', 'Image aléatoire pour Cork 720', '2025-03-21 14:28:24'),
(251, 94, 'uploads/figure_67dd7788077ce_image8.jpg', 'Image aléatoire pour Cork 720', '2025-03-21 14:28:24'),
(252, 95, 'uploads/figure_67dd77880d9be_image11.jpg', 'Image aléatoire pour Nose Slide', '2025-03-21 14:28:24'),
(253, 95, 'uploads/figure_67dd778814c10_image6.jpg', 'Image aléatoire pour Nose Slide', '2025-03-21 14:28:24'),
(254, 95, 'uploads/figure_67dd77881adbe_image9.jpg', 'Image aléatoire pour Nose Slide', '2025-03-21 14:28:24'),
(255, 96, 'uploads/figure_67dd778820df3_image4.jpg', 'Image aléatoire pour Tail Grab', '2025-03-21 14:28:24'),
(256, 96, 'uploads/figure_67dd778826be4_image5.jpg', 'Image aléatoire pour Tail Grab', '2025-03-21 14:28:24'),
(257, 96, 'uploads/figure_67dd77882ca6a_image9.jpg', 'Image aléatoire pour Tail Grab', '2025-03-21 14:28:24'),
(258, 97, 'uploads/figure_67dd778832955_image1.jpg', 'Image aléatoire pour Truck Driver', '2025-03-21 14:28:24'),
(259, 97, 'uploads/figure_67dd7788389d9_image2.jpg', 'Image aléatoire pour Truck Driver', '2025-03-21 14:28:24'),
(260, 97, 'uploads/figure_67dd77883e990_image3.jpg', 'Image aléatoire pour Truck Driver', '2025-03-21 14:28:24'),
(261, 98, 'uploads/figure_67dd778844af6_image1.jpg', 'Image aléatoire pour Rocket Air', '2025-03-21 14:28:24'),
(262, 98, 'uploads/figure_67dd77884b680_image10.jpg', 'Image aléatoire pour Rocket Air', '2025-03-21 14:28:24'),
(263, 98, 'uploads/figure_67dd778851f10_image8.jpg', 'Image aléatoire pour Rocket Air', '2025-03-21 14:28:24');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `activation_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `is_active`, `activation_token`, `reset_token`, `reset_token_expires_at`) VALUES
(38, 'demo_user', 'demo@example.com', '$2y$13$X3LsSClZIxa9agNduvVn3emiFhmmDX17wQ1CyuVgYD9HBjNe/XkyK', 1, NULL, NULL, NULL),
(39, 'jane_doe', 'jane@example.com', '$2y$13$Y/4wHXwE.AUm/UrDl2KXTel2N0RjFIlB3jMt6H0N.VZ/CQ6D6OebO', 1, NULL, NULL, NULL),
(40, 'john_smith', 'john@example.com', '$2y$13$0rNGlFG08AfxPYWRejoX8uyQ3IRmVOBvoU86rLQng6snRpyTYtere', 1, NULL, NULL, NULL),
(41, 'alex_hawk', 'alex@example.com', '$2y$13$Ei7znlDTvwwmo1egUJ5ebeT4k/GIak/EcR1v8KbS5LWqT0ExnS4HS', 1, NULL, NULL, NULL),
(42, 'marie_lake', 'marie@example.com', '$2y$13$aAsgcRlepDm89RiGHuUgF.XAIm5KoJpWz3Ztkfp7no/7LV7irK9uy', 1, NULL, NULL, NULL),
(43, 'paul_rider', 'paul@example.com', '$2y$13$.MCBjhblkqwXwI2IpBmHXeoC8FpqPhkPGzxzEBI0yPX6AHhwoRJfW', 1, NULL, NULL, NULL),
(44, 'AdrienF', 'adrien@mail.com', '$2y$13$txG.onSpv21BAEqnv2JJo.vDMfVoqsNjn8AOeiA/yE6Dv0u9jT/ZS', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `video`
--

CREATE TABLE `video` (
  `id` int NOT NULL,
  `figure_id` int NOT NULL,
  `embed_code` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `video`
--

INSERT INTO `video` (`id`, `figure_id`, `embed_code`, `created_at`) VALUES
(76, 89, '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/example\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>', '2025-03-21 14:28:23'),
(77, 90, '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/example\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>', '2025-03-21 14:28:23'),
(78, 91, '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/example\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>', '2025-03-21 14:28:23'),
(79, 92, '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/example\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>', '2025-03-21 14:28:23'),
(80, 93, '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/example\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>', '2025-03-21 14:28:23'),
(81, 94, '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/example\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>', '2025-03-21 14:28:24'),
(82, 95, '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/example\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>', '2025-03-21 14:28:24'),
(83, 96, '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/example\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>', '2025-03-21 14:28:24'),
(84, 97, '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/example\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>', '2025-03-21 14:28:24'),
(85, 98, '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/example\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>', '2025-03-21 14:28:24');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9474526CF675F31B` (`author_id`),
  ADD KEY `IDX_9474526C5C011B5` (`figure_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `figure`
--
ALTER TABLE `figure`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_2F57B37A5E237E06` (`name`),
  ADD UNIQUE KEY `UNIQ_2F57B37AE4873418` (`main_image_id`),
  ADD KEY `IDX_2F57B37AF675F31B` (`author_id`);

--
-- Index pour la table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C53D045F5C011B5` (`figure_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- Index pour la table `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7CC7DA2C5C011B5` (`figure_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=484;

--
-- AUTO_INCREMENT pour la table `figure`
--
ALTER TABLE `figure`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT pour la table `image`
--
ALTER TABLE `image`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=264;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT pour la table `video`
--
ALTER TABLE `video`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526C5C011B5` FOREIGN KEY (`figure_id`) REFERENCES `figure` (`id`),
  ADD CONSTRAINT `FK_9474526CF675F31B` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `figure`
--
ALTER TABLE `figure`
  ADD CONSTRAINT `FK_2F57B37AE4873418` FOREIGN KEY (`main_image_id`) REFERENCES `image` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_2F57B37AF675F31B` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `FK_C53D045F5C011B5` FOREIGN KEY (`figure_id`) REFERENCES `figure` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `video`
--
ALTER TABLE `video`
  ADD CONSTRAINT `FK_7CC7DA2C5C011B5` FOREIGN KEY (`figure_id`) REFERENCES `figure` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
