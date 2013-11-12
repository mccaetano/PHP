USE [quebarato]
GO

/****** Object:  Table [dbo].[wtb_integracao_quebarato]    Script Date: 09/26/2013 11:56:21 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[wtb_integracao_quebarato](
	[cd_integracao] [int] IDENTITY(1,1) NOT NULL,
	[id_veiculo] [int] NULL,
	[id_quebarato] [int] NULL,
	[cnpj] [varchar](14) NULL,
 CONSTRAINT [PK_wtb_inegracao_quebarato] PRIMARY KEY CLUSTERED 
(
	[cd_integracao] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]

GO

