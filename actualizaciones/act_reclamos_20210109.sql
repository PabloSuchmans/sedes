CREATE TRIGGER `clientes_bu` BEFORE UPDATE ON `clientes` FOR EACH ROW  
begin
  set new.fecha_actualizacion=CURRENT_TIMESTAMP;
end