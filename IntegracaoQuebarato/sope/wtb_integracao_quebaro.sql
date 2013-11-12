USE [itcar]
GO

/****** Object:  Table [itcar].[wtb_integracao_quebarato]    Script Date: 10/06/2013 01:07:57 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [itcar].[wtb_integracao_quebarato](
	[cd_integracao] [int] IDENTITY(1,1) NOT NULL,
	[id_veiculo] [int] NULL,
	[id_quebarato] [int] NULL,
	[cnpj] [varchar](14) NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO


