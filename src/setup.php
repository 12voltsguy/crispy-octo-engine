<?php

include 'db.php';
include_once 'logging.php';

$result = DB::query("SELECT * FROM sysobjects WHERE xtype='U'");

$pretty = json_encode($result);

print_r($pretty);



###################################
###### rcare_account table ######## 
###################################
$rcare_account = "USE [r5000]
GO

/****** Object:  Table [dbo].[rcare_account]    Script Date: 5/6/2022 3:34:55 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

If ( not exists
CREATE TABLE [dbo].[rcare_account](
	[account] [int] NOT NULL,
	[name] [nvarchar](50) NULL,
	[address1] [nvarchar](50) NULL,
	[address2] [nvarchar](50) NULL,
 CONSTRAINT [constraintName] PRIMARY KEY CLUSTERED 
(
	[account] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY]

GO";

###################################
######    incident table   ######## 
###################################

$incident = "USE [r5000]
GO

/****** Object:  Table [dbo].[Incident]    Script Date: 5/6/2022 3:34:17 PM ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[Incident](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[account] [int] NULL,
	[active] [varchar](10) NULL,
	[holdoff] [varchar](10) NULL,
	[closed] [varchar](10) NULL,
	[start_time] [datetime] NOT NULL CONSTRAINT [DF_Incident_start_time]  DEFAULT (getdate()),
	[zone] [int] NULL,
	[device_name] [varchar](50) NULL,
	[account_name] [varchar](50) NULL,
	[device_id] [int] NULL,
	[num] [int] NULL,
	[stop_time] [nchar](10) NULL,
	[alarm_pid] [nchar](10) NULL,
	[reset_device] [int] NULL,
	[account_address] [varchar](max) NULL,
	[account_group] [nchar](10) NULL,
	[dialer_received] [ntext] NULL,
	[type] [int] NULL,
	[locators] [varchar](50) NULL,
	[locator_nums] [int] NULL,
	[dialer_state] [smallint] NULL,
	[notif_group] [smallint] NULL,
	[locator1] [varchar](50) NULL,
	[cs_notified] [nchar](10) NULL,
	[got_it] [nchar](10) NULL,
	[got_it_time] [nchar](10) NULL,
	[floor] [nchar](10) NULL,
	[location_description] [nchar](10) NULL,
 CONSTRAINT [PK_Incident] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO";


?>
