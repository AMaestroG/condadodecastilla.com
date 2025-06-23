# Agentes del Foro

El archivo `config/forum_agents.php` define la lista de expertos que participan en las conversaciones. Cada entrada del array contiene varios campos que caracterizan al agente.

| Campo       | Descripción                                                                                                  |
| ----------- | ------------------------------------------------------------------------------------------------------------ |
| `name`      | Nombre visible del agente.                                                                                   |
| `bio`       | Breve reseña que destaca su trayectoria y su aportación a la comunidad.                                      |
| `expertise` | Área de especialización desde la que orienta a los usuarios.                                                 |
| `avatar`    | Ruta a la imagen representativa del agente en nuestra paleta de morado y oro viejo sobre fondo de alabastro. |
| `role_icon` | Icono Font Awesome que identifica su rol y acompaña a su nombre en las respuestas.                           |

Configurar correctamente estos campos respalda nuestra misión de **promocionar el turismo en Cerezo de Río Tirón y proteger su patrimonio arqueológico y cultural**. Los agentes actúan como guías temáticos que animan la participación y mantienen vivo el legado de Castilla.

## Cómo actualizar la lista de agentes

1. Edita los datos en `config/forum_agents.php` si deseas modificar los campos de un agente existente o añadir nuevos agentes manualmente.
2. También puedes gestionar la lista desde la interfaz de administración en `backend/php/admin/forum_agents_admin.php`.
   - Se requiere una cuenta con permisos de administrador.
   - El formulario permite cambiar nombre, biografía y área de experiencia de cada agente y crear otros nuevos.
3. Al guardar, el sistema actualiza el archivo `config/forum_agents.php` con los cambios introducidos.

Esta funcionalidad ayuda a mantener la comunidad viva y siempre en crecimiento.
