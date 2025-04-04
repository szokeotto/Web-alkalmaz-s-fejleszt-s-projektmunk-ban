-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2022. Máj 01. 22:16
-- Kiszolgáló verziója: 10.4.22-MariaDB
-- PHP verzió: 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `php_vizsga`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `codes`
--

CREATE TABLE `codes` (
  `userid` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `time_upload` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- A tábla adatainak kiíratása `codes`
--

INSERT INTO `codes` (`userid`, `code`, `time_upload`) VALUES
(0, 'xde8ikakrnt6929vst6is512', '2022-05-01 18:06:51'),
(4, 'szoke89ht3bd76gaqoprn381', '2022-05-01 20:52:54'),
(4, 'acbe58pt98tg2bd76gstvaqa', '2022-05-01 21:00:35'),
(4, '123456789asdfghjklqwertz', '2022-05-01 21:01:48'),
(5, 'kasza6gt3987gva29h6dsw21', '2022-05-01 21:03:01'),
(5, 'blanka6giolfeb8957klosb2', '2022-05-01 21:03:34'),
(5, '987654321poiuztrewqlkjsb', '2022-05-01 22:05:36'),
(4, 'qwertzuiop9876543210asdf', '2022-05-01 22:06:22'),
(4, '258963147895412587412569', '2022-05-01 22:14:42');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `sessions`
--

CREATE TABLE `sessions` (
  `sid` varchar(64) NOT NULL,
  `spass` varchar(64) NOT NULL,
  `stime` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `userid` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(80) NOT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `time_created` datetime NOT NULL,
  `time_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`userid`, `name`, `email`, `password`, `status`, `time_created`, `time_updated`) VALUES
(1, 'Teszt Elek', 'teszt@elek.hu', '123456', 1, '2022-04-30 22:15:53', '2022-04-30 22:15:53'),
(4, 'Szőke Ottó', 'szoke.otto@gmail.com', '$2y$10$aqCL/R8tSrqrvc/soFdJaeRtsmmYB47cYEsw/x7V0figrodlELD2K', 1, '2022-05-01 15:52:02', NULL),
(5, 'Kasza Blanka', 'blanka@kasza.com', '$2y$10$xlx2a4triUZ3z4jwPGSBVuMpUjVhoayFK3HcMElyoVmtuzNZXf/PW', 1, '2022-05-01 17:18:18', NULL);

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `codes`
--
ALTER TABLE `codes`
  ADD PRIMARY KEY (`time_upload`),
  ADD UNIQUE KEY `code` (`code`);

--
-- A tábla indexei `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`sid`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`),
  ADD KEY `email` (`email`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
