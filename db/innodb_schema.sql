
CREATE TABLE `measurement` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `measurement_entry` (
  `id` int(11) NOT NULL,
  `measurement_id` int(11) DEFAULT NULL,
  `value` float DEFAULT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `measurement_unit` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `measurement_unit` (`id`, `name`) VALUES
(1, 'kg'),
(2, 'cm');

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `password` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `registered` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


ALTER TABLE `measurement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_measurement_user_id` (`user_id`),
  ADD KEY `fk_measurement_unit_id` (`unit_id`);

ALTER TABLE `measurement_entry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_measurement_entry_id` (`measurement_id`);

ALTER TABLE `measurement_unit`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `measurement`
  ADD CONSTRAINT `fk_measurement_unit_id` FOREIGN KEY (`unit_id`) REFERENCES `measurement_unit` (`id`),
  ADD CONSTRAINT `fk_measurement_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

ALTER TABLE `measurement_entry`
  ADD CONSTRAINT `fk_measurement_entry_id` FOREIGN KEY (`measurement_id`) REFERENCES `measurement` (`id`);