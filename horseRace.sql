SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `horserace`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `horses`
--

CREATE TABLE `horses` (
  `id` int(11) NOT NULL,
  `raceId` int(11) NOT NULL,
  `speed` float NOT NULL,
  `strength` float NOT NULL,
  `endurance` float NOT NULL,
  `timeNeeded` int(11) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `races`
--

CREATE TABLE `races` (
  `id` int(11) NOT NULL,
  `timeRun` int(11) NOT NULL DEFAULT '0',
  `timeStarted` datetime NOT NULL,
  `timeFinished` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `horses`
--
ALTER TABLE `horses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `race` (`raceId`);

--
-- Indizes für die Tabelle `races`
--
ALTER TABLE `races`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `horses`
--
ALTER TABLE `horses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `races`
--
ALTER TABLE `races`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `horses`
--
ALTER TABLE `horses`
  ADD CONSTRAINT `race` FOREIGN KEY (`raceId`) REFERENCES `races` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
