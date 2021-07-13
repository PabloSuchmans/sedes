CREATE TRIGGER `reclamos_bu` BEFORE UPDATE ON `reclamos` FOR EACH ROW  
begin
  set new.fecha_actualizacion=CURRENT_TIMESTAMP;
end