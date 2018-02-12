--
-- 表的结构 `cs_filedata`
--

CREATE TABLE `cs_filedata` (
  `fileid` bigint(16) UNSIGNED NOT NULL COMMENT '文件id',
  `filetype` varchar(255) NOT NULL COMMENT '文件类型 见A.26',
  `path` varchar(1024) NOT NULL COMMENT '文件路径'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文件上传信息表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `filedata`
--
ALTER TABLE `cs_filedata`
  ADD PRIMARY KEY (`fileid`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `filedata`
--
ALTER TABLE `cs_filedata`
  MODIFY `fileid` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文件id';