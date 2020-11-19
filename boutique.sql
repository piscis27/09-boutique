-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 13 nov. 2020 à 14:09
-- Version du serveur :  10.4.14-MariaDB
-- Version de PHP : 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `boutique`
--

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
  `id_commande` int(11) NOT NULL,
  `membre_id` int(11) NOT NULL,
  `montant` int(11) NOT NULL,
  `date_enregistrement` datetime NOT NULL,
  `etat` enum('en_cours_de_traitement','envoye','livre') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `commande`
--

INSERT INTO `commande` (`id_commande`, `membre_id`, `montant`, `date_enregistrement`, `etat`) VALUES
(9, 19, 374, '2020-11-09 15:09:18', 'en_cours_de_traitement'),
(10, 19, 374, '2020-11-09 15:20:56', 'envoye'),
(11, 19, 94, '2020-11-09 15:26:57', 'livre'),
(12, 18, 621, '2020-11-10 15:06:44', 'en_cours_de_traitement'),
(13, 20, 282, '2020-11-10 15:07:30', 'envoye'),
(14, 24, 235, '2020-11-10 15:08:18', 'livre'),
(15, 19, 15, '2020-11-10 23:05:05', 'en_cours_de_traitement');

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

CREATE TABLE `commentaire` (
  `id_commentaire` int(11) NOT NULL,
  `pseudo` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `date_enregistrement` datetime NOT NULL,
  `produit_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `details_commande`
--

CREATE TABLE `details_commande` (
  `id_details_commande` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `produit_id` int(11) NOT NULL,
  `quantite` int(5) NOT NULL,
  `prix` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `details_commande`
--

INSERT INTO `details_commande` (`id_details_commande`, `commande_id`, `produit_id`, `quantite`, `prix`) VALUES
(22, 9, 31, 6, 30),
(23, 9, 33, 7, 17),
(24, 9, 32, 3, 25),
(25, 10, 31, 6, 30),
(26, 10, 33, 7, 17),
(27, 10, 32, 3, 25),
(28, 11, 33, 2, 17),
(29, 11, 29, 4, 15),
(30, 12, 29, 8, 15),
(31, 12, 30, 9, 50),
(32, 12, 33, 3, 17),
(33, 13, 31, 3, 30),
(34, 13, 32, 7, 25),
(35, 13, 33, 1, 17),
(36, 14, 31, 2, 30),
(37, 14, 32, 7, 25),
(38, 15, 29, 1, 15);

-- --------------------------------------------------------

--
-- Structure de la table `membre`
--

CREATE TABLE `membre` (
  `id_membre` int(11) NOT NULL,
  `pseudo` varchar(20) NOT NULL,
  `mdp` varchar(60) NOT NULL,
  `nom` varchar(20) NOT NULL,
  `prenom` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `civilite` enum('homme','femme') NOT NULL,
  `ville` varchar(20) NOT NULL,
  `code_postal` int(5) UNSIGNED ZEROFILL NOT NULL,
  `adresse` text NOT NULL,
  `statut` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `membre`
--

INSERT INTO `membre` (`id_membre`, `pseudo`, `mdp`, `nom`, `prenom`, `email`, `civilite`, `ville`, `code_postal`, `adresse`, `statut`) VALUES
(18, 'titi78', '$2y$10$mmIvV96tWLsJTfe4YILzv.qxAjUKw.iRaUZn9R/3RHlvpTlfrIOkS', 'titi78', 'titi78', 'titi78@gmail.com', 'femme', 'titiland', 78002, '45 rue de titi78', 0),
(19, 'GregFormateur', '$2y$10$Nbjh0ZrgpRDQzRxhsgaVweQoOmoQ9ZMmk6xQdZfg2WrmXcfiCErNC', 'Grégory', 'Lacroix', 'gregorylacroix78@gmail.com', 'homme', 'Gambais', 78950, '45 Rue des Vieilles tuileries', 1),
(20, 'toto78', '$2y$10$Jhcwb/MVmSzjJL6femEmU.mTxCwCT1FC5IPZwDEhVs2Sk2NKqrm52', 'toto78', 'toto78', 'toto78@gmail.com', 'femme', 'totoland', 78000, '45 rue de toto78', 0),
(21, 'tata78', '$2y$10$rKIC2KFau/VBUqb3lELgK.cncB0hCjjrN2sxq0CMBrnlflfXuFdwi', 'tata78', 'tata78', 'tata78@gmail.com', 'femme', 'tataland', 72450, '45 rue de tata78', 1),
(22, 'tutu78', '$2y$10$/mWxHvruwEHyteF3mbPopOHXbdEDHcXLGHJ1n8WcS2RmYwm228BAu', 'tutu78', 'tutu78', 'tutu78@gmail.com', 'femme', 'tutuland', 78000, '45 rue de tutu', 0),
(24, 'tete78', '$2y$10$f7TsMxOXJ3fI7P11CFbww.QlReNAjI00mBYu4eLwGzmzgLXVaTQui', 'tete78', 'tete78', 'tete78@gmail.com', 'homme', 'tetelande', 75000, '45 rue de tete78', 0);

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE `produit` (
  `id_produit` int(11) NOT NULL,
  `reference` varchar(20) NOT NULL,
  `categorie` varchar(20) NOT NULL,
  `titre` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `couleur` varchar(20) NOT NULL,
  `taille` varchar(5) NOT NULL,
  `public` enum('homme','femme','mixte') NOT NULL,
  `photo` varchar(250) NOT NULL,
  `prix` int(5) NOT NULL,
  `stock` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id_produit`, `reference`, `categorie`, `titre`, `description`, `couleur`, `taille`, `public`, `photo`, `prix`, `stock`) VALUES
(29, '15A45', 'Tee-shirt', 'Tee-shirt bleu', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla et iaculis tellus. Proin a arcu ut ante aliquam tempus. Proin id blandit quam, sed pharetra ex. Pellentesque odio ex, convallis eget luctus sed, eleifend suscipit sapien. Sed ut facilisis tellus, porttitor porta purus. Nam feugiat maximus enim eget porttitor. Phasellus diam erat, lobortis a semper in, accumsan non dolor. Nunc tristique quis ex a venenatis. Donec enim turpis, tincidunt a porttitor luctus, tempor vitae orci. Phasellus augue tellus, bibendum eget tristique quis, tincidunt ac lacus. In finibus a ex eget lacinia. Sed sagittis porta nisi sollicitudin aliquam. Cras quis tempor nisi, at placerat lectus. Donec dictum at augue a mollis.', 'Bleu', 'M', 'homme', 'http://localhost/PHP/09-boutique/photo/15A45-tee-shirt-5.jpg', 15, 137),
(30, '45A78', 'Pull', 'Pull vert', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla et iaculis tellus. Proin a arcu ut ante aliquam tempus. Proin id blandit quam, sed pharetra ex. Pellentesque odio ex, convallis eget luctus sed, eleifend suscipit sapien. Sed ut facilisis tellus, porttitor porta purus. Nam feugiat maximus enim eget porttitor. Phasellus diam erat, lobortis a semper in, accumsan non dolor. Nunc tristique quis ex a venenatis. Donec enim turpis, tincidunt a porttitor luctus, tempor vitae orci. Phasellus augue tellus, bibendum eget tristique quis, tincidunt ac lacus. In finibus a ex eget lacinia. Sed sagittis porta nisi sollicitudin aliquam. Cras quis tempor nisi, at placerat lectus. Donec dictum at augue a mollis.', 'Vert', 'M', 'homme', 'http://localhost/PHP/09-boutique/photo/45A78-tee-shirt-6.jpg', 50, 111),
(31, '19L56', 'chemise', 'chemise noir', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla et iaculis tellus. Proin a arcu ut ante aliquam tempus. Proin id blandit quam, sed pharetra ex. Pellentesque odio ex, convallis eget luctus sed, eleifend suscipit sapien. Sed ut facilisis tellus, porttitor porta purus. Nam feugiat maximus enim eget porttitor. Phasellus diam erat, lobortis a semper in, accumsan non dolor. Nunc tristique quis ex a venenatis. Donec enim turpis, tincidunt a porttitor luctus, tempor vitae orci. Phasellus augue tellus, bibendum eget tristique quis, tincidunt ac lacus. In finibus a ex eget lacinia. Sed sagittis porta nisi sollicitudin aliquam. Cras quis tempor nisi, at placerat lectus. Donec dictum at augue a mollis.', 'noir', 'XL', 'homme', 'http://localhost/PHP/09-boutique/photo/19L56-tee-shirt-5.jpg', 30, 127),
(32, '23P45', 'Tee-shirt', 'Tee-shirt violet', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla et iaculis tellus. Proin a arcu ut ante aliquam tempus. Proin id blandit quam, sed pharetra ex. Pellentesque odio ex, convallis eget luctus sed, eleifend suscipit sapien. Sed ut facilisis tellus, porttitor porta purus. Nam feugiat maximus enim eget porttitor. Phasellus diam erat, lobortis a semper in, accumsan non dolor. Nunc tristique quis ex a venenatis. Donec enim turpis, tincidunt a porttitor luctus, tempor vitae orci. Phasellus augue tellus, bibendum eget tristique quis, tincidunt ac lacus. In finibus a ex eget lacinia. Sed sagittis porta nisi sollicitudin aliquam. Cras quis tempor nisi, at placerat lectus. Donec dictum at augue a mollis.', 'violet', 'L', 'homme', 'http://localhost/PHP/09-boutique/photo/23P45-tee-shirt-6.jpg', 25, 52),
(33, '19S73', 'Pull', 'pull marron', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla et iaculis tellus. Proin a arcu ut ante aliquam tempus. Proin id blandit quam, sed pharetra ex. Pellentesque odio ex, convallis eget luctus sed, eleifend suscipit sapien. Sed ut facilisis tellus, porttitor porta purus. Nam feugiat maximus enim eget porttitor. Phasellus diam erat, lobortis a semper in, accumsan non dolor. Nunc tristique quis ex a venenatis. Donec enim turpis, tincidunt a porttitor luctus, tempor vitae orci. Phasellus augue tellus, bibendum eget tristique quis, tincidunt ac lacus. In finibus a ex eget lacinia. Sed sagittis porta nisi sollicitudin aliquam. Cras quis tempor nisi, at placerat lectus. Donec dictum at augue a mollis.', 'marron', 'S', 'homme', 'http://localhost/PHP/09-boutique/photo/19S73-tee-shirt-3.jpg', 17, 66);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `membre_id` (`membre_id`);

--
-- Index pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD PRIMARY KEY (`id_commentaire`);

--
-- Index pour la table `details_commande`
--
ALTER TABLE `details_commande`
  ADD PRIMARY KEY (`id_details_commande`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `membre`
--
ALTER TABLE `membre`
  ADD PRIMARY KEY (`id_membre`),
  ADD UNIQUE KEY `pseudo` (`pseudo`);

--
-- Index pour la table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id_produit`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
  MODIFY `id_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `commentaire`
--
ALTER TABLE `commentaire`
  MODIFY `id_commentaire` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `details_commande`
--
ALTER TABLE `details_commande`
  MODIFY `id_details_commande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `membre`
--
ALTER TABLE `membre`
  MODIFY `id_membre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `produit`
--
ALTER TABLE `produit`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`membre_id`) REFERENCES `membre` (`id_membre`);

--
-- Contraintes pour la table `details_commande`
--
ALTER TABLE `details_commande`
  ADD CONSTRAINT `details_commande_ibfk_1` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id_produit`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
