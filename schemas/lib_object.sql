ALTER TABLE `lib_object` ADD `default_count` INT NOT NULL DEFAULT '0' AFTER `code`;
INSERT INTO `lib_object` (`code`, `default_count`) VALUES
('chip', 1000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lib_object`
--
ALTER TABLE `lib_object`
  ADD PRIMARY KEY (`code`);

