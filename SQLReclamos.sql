select 
    clientes.numecli as nro_cliente,
    reclamos.*,
    DATE_FORMAT(reclamos.fecha_hora_creacion, '%d-%b %H:%i') as fecha_hora_creacion_format,
    DATE_FORMAT(reclamos.fecha_hora_resolucion, '%d-%b %H:%i') as fecha_hora_resolucion_format,
    clientes.apellido as nombre_cli, 
    clientes.calle as direccion_cli, 
    clientes.tel as telefono_cli, 
    clientes.IP as ip_cli, 
    clientes.MAC as mac_cli,
    consorcios.codigo as codigo_con,
    consorcios.descripcion as descripcion_con,
    consorcios.usuario as usuario_con,
    consorcios.telefono as telefono_con,
    consorcios.direccion as direccion_con,
    consorcios.tipoacceso as tipoacceso_con,
    consorcios.usuario as usuario_con,
    consorcios.password as password_con,
    consorcios.modem as modem_con,
    usuario_creacion.nombre as nom_usuario_creacion,
    usuario_asignado.nombre as nom_usuario_asignado,
    usuario_resolucion.nombre as nom_usuario_resolucion,
    case 
      when 
        reclamos.nro_usuario_resolucion <> 0 then 'Resuelto'
      when 
        (reclamos.nro_usuario_resolucion = 0) and  
        (reclamos.nro_usuario_asignado <> 0) then 'Asignado'
      when 
        (reclamos.nro_usuario_resolucion = 0) and  
        (reclamos.nro_usuario_asignado = 0) then 'Pendiente'
    end as nom_estado,
    case 
      when 
        reclamos.nro_usuario_resolucion <> 0 then 3
      when 
        (reclamos.nro_usuario_resolucion = 0) and  
        (reclamos.nro_usuario_asignado <> 0) then 2
      when 
        (reclamos.nro_usuario_resolucion = 0) and  
        (reclamos.nro_usuario_asignado = 0) then 1
    end as nro_estado
  from reclamos
  inner join clientes on clientes.numecli = reclamos.nro_cliente
  inner join consorcios on consorcios.codigo = clientes.codigo_consor
  inner join usuarios usuario_creacion on usuario_creacion.nro_usuario = reclamos.nro_usuario_creacion
  left join usuarios usuario_asignado on usuario_asignado.nro_usuario = reclamos.nro_usuario_asignado
  left join usuarios usuario_resolucion on usuario_resolucion.nro_usuario = reclamos.nro_usuario_resolucion
