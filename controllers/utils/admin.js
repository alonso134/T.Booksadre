/*
*   Controlador de uso general en las páginas web del sitio privado.
*   Sirve para manejar la plantilla del encabezado y pie del documento.
*/

// Constante para completar la ruta de la API.
const USER_API = 'services/admin/administrador.php';
// Constante para establecer el elemento del contenido principal.
const MAIN = document.querySelector('main');
MAIN.style.paddingTop = '75px';
MAIN.style.paddingBottom = '100px';
MAIN.classList.add('container');
// Se establece el título de la página web.
document.querySelector('title').textContent = 'booksadre - Dashboard';
// Constante para establecer el elemento del título principal.
const MAIN_TITLE = document.getElementById('mainTitle');
MAIN_TITLE.classList.add('text-center', 'py-3');

/*  Función asíncrona para cargar el encabezado y pie del documento.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const loadTemplate = async () => {
    // Petición para obtener en nombre del usuario que ha iniciado sesión.
    const DATA = await fetchData(USER_API, 'getUser');
    // Se verifica si el usuario está autenticado, de lo contrario se envía a iniciar sesión.
    if (DATA.session) {
        // Se comprueba si existe un alias definido para el usuario, de lo contrario se muestra un mensaje con la excepción.
        if (DATA.status) {
            // Se agrega el encabezado de la página web antes del contenido principal.
            MAIN.insertAdjacentHTML('beforebegin', `
                <header>
                <div class="menu ">
                <nav class="navbar bg-body-tertiary fixed-top ">
                  <div class="container-fluid ">
               
                    <a class="navbar-brand"><img src="../../resources/img/logo.png" alt="Logo"></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                      <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                      <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Administrador</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                      </div>
                      <div class="offcanvas-body">
                        <div class="circle-container">
                          <div class="circle">
                            <img src="https://cdn-icons-png.flaticon.com/512/456/456283.png" alt="Imagen en un círculo">
                          </div>
              
                        </div>          
              
                        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                          
              
                          <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="../../views/admin/Inicio.html"><i class='bx bxs-home-alt-2' ></i> Inicio</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="../../views/admin/Categorias.html"><i class='bx bxs-book-bookmark' ></i> Categorías</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" ria-current="page" href="../../views/admin/Productos.html"><i class='bx bxs-pie-chart-alt-2'></i> Productos</a>
                          </li>
                          <li class="nav-item ">
                          <a class="nav-link active" aria-current="page" href="../../views/admin/Pedidos.html"><i class='bx bxs-report'></i> Pedidos</a>
                        </li>
                        <li class="nav-item ">
                          <a class="nav-link active" aria-current="page" href="../../views/admin/valoracion.html"><i class='bx bxs-report'></i> Valoraciones</a>
                        </li>
                          <li class="nav-item">
                            <a class="nav-link" href="../../views/admin/Clientes.html"><i class='bx bxs-user'></i> Clientes</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="../../views/admin/Administrador.html"><i class='bx bxs-user-plus' ></i> Administradores</a>
                          </li>
                          <li class="nav-item dropdown">
                              <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">Cuenta: <b>${DATA.username}</b></a>
                              <ul class="dropdown-menu">
                                  <li><a class="dropdown-item" href="profile.html">Editar perfil</a></li>
                                  <li><hr class="dropdown-divider"></li>
                                  <li><a class="dropdown-item" href="#" onclick="logOut()">Cerrar sesión</a></li>
                              </ul>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </nav>
              </div>
              
              
                </header>
            `);
        
        } else {
            sweetAlert(3, DATA.error, false, 'index.html');
        }
    } else {
        // Se comprueba si la página web es la principal, de lo contrario se direcciona a iniciar sesión.
        if (location.pathname.endsWith('index.html')) {
            // Se agrega el encabezado de la página web antes del contenido principal.
            MAIN.insertAdjacentHTML('beforebegin', `
                <header>
                    <nav class="navbar fixed-top bg-body-tertiary">
                        <div class="container">
                            <a class="navbar-brand" href="index.html">
                                <img src="../../resources/img/logo.png" alt="inventory" width="50">
                            </a>
                        </div>
                    </nav>
                </header>
            `);
            // Se agrega el pie de la página web después del contenido principal.
            MAIN.insertAdjacentHTML('afterend', `
                <footer>
                    <nav class="navbar fixed-bottom bg-body-tertiary">
                        <div class="container">
                            <p><a class="nav-link" href="https://github.com/milesssssssss/Tienda" target="_blank"><i class="bi bi-github"></i> booksadre</a></p>
                            <p><i class="bi bi-envelope-fill"></i> booksadre23@gmail.com</p>
                        </div>
                    </nav>
                </footer>
            `);
        } else {
            location.href = 'index.html';
        }
    }
}