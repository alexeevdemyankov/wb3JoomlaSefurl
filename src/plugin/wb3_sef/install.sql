
CREATE TABLE `wb3_sefurl` (
                            `id_sefurl` int(11) NOT NULL,
                            `origurl_sefurl` varchar(512) NOT NULL,
                            `sefurl_sefurl` varchar(512) NOT NULL,
                            `enable_sefurl` int(11) NOT NULL,
                            `title_sefurl` varchar(512) NOT NULL,
                            `description_sefurl` varchar(512) NOT NULL,
                            `keywords_sefurl` text NOT NULL,
                            `redirect_sefurl` varchar(254) NOT NULL,
                            `redirect_code_sefurl` varchar(128) NOT NULL,
                            `external_scripts` longtext NOT NULL,
                            `adv_meta_sefurl` text NOT NULL,
                            `jmenu_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `wb3_sefurl`
  ADD UNIQUE KEY `id_sefurl` (`id_sefurl`),
  ADD KEY `origurl_sefurl` (`origurl_sefurl`),
  ADD KEY `sefurl_sefurl` (`sefurl_sefurl`),
  ADD KEY `redirect_sefurl` (`redirect_sefurl`);


ALTER TABLE `wb3_sefurl`
  MODIFY `id_sefurl` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;