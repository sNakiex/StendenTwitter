StendenTwitter
==================
Required Database:

--
-- Tabelstructuur voor tabel `stenden_messages`
--

CREATE TABLE IF NOT EXISTS `stenden_messages` (
  `msgId` int(60) NOT NULL,
  `userId` int(6) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `stenden_users`
--

CREATE TABLE IF NOT EXISTS `stenden_users` (
  `userId` int(4) NOT NULL,
  `userIp` varchar(30) NOT NULL,
  `userName` varchar(20) NOT NULL,
  `userPass` varchar(255) NOT NULL,
  `userSalt` varchar(120) NOT NULL,
  `userEmail` varchar(32) NOT NULL,
  `userImagePath` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `stenden_messages`
--
ALTER TABLE `stenden_messages`
  ADD PRIMARY KEY (`msgId`);

--
-- Indexen voor tabel `stenden_users`
--
ALTER TABLE `stenden_users`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `stenden_messages`
--
ALTER TABLE `stenden_messages`
  MODIFY `msgId` int(60) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT voor een tabel `stenden_users`
--
ALTER TABLE `stenden_users`
  MODIFY `userId` int(4) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;