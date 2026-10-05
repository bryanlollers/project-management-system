-- Run once in Supabase SQL Editor before Laravel migrations.
-- Keep pms OUT of the exposed Data API schemas.
CREATE SCHEMA IF NOT EXISTS pms AUTHORIZATION postgres;
REVOKE ALL ON SCHEMA pms FROM PUBLIC, anon, authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA pms
    REVOKE ALL ON TABLES FROM anon, authenticated;
ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA pms
    REVOKE ALL ON SEQUENCES FROM anon, authenticated;
