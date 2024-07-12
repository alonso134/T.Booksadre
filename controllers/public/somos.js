document.addEventListener("DOMContentLoaded", function () {
    // Crear contenedor principal
    const rowDiv = document.createElement('div');
    rowDiv.className = 'row';

    // Crear columna izquierda con el menú de navegación
    const col4Div = document.createElement('div');
    col4Div.className = 'col-4';

    const navMain = document.createElement('nav');
    navMain.id = 'navbar-example3';
    navMain.className = 'h-100 flex-column align-items-stretch pe-4 border-end';

    const navPills = document.createElement('nav');
    navPills.className = 'nav nav-pills flex-column';

    const links = [
        { href: '#item-1', text: 'Misión' },
        { href: '#item-2', text: 'Visión' },
        { href: '#item-3', text: 'Valores' },
        { href: '#item-4', text: 'Patrocinadores y socios' },
        { href: '#item-5', text: 'Marcas' },
        { href: '#item-6', text: 'Clientes destacados' }
    ];

    links.forEach(link => {
        const a = document.createElement('a');
        a.className = 'nav-link';
        a.href = link.href;
        a.textContent = link.text;
        navPills.appendChild(a);
    });

    navMain.appendChild(navPills);
    col4Div.appendChild(navMain);
    rowDiv.appendChild(col4Div);

    // Crear columna derecha con el contenido
    const col8Div = document.createElement('div');
    col8Div.className = 'col-8';

    const scrollSpyDiv = document.createElement('div');
    scrollSpyDiv.setAttribute('data-bs-spy', 'scroll');
    scrollSpyDiv.setAttribute('data-bs-target', '#navbar-example3');
    scrollSpyDiv.setAttribute('data-bs-smooth-scroll', 'true');
    scrollSpyDiv.className = 'scrollspy-example-2';
    scrollSpyDiv.tabIndex = 0;

    const sections = [
        { id: 'item-1', title: 'Misión', content: 'Nuestra misión es proporcionar a nuestros clientes una amplia gama de útiles escolares y libros, promoviendo la educación y el aprendizaje a través de productos de calidad y un servicio excepcional.' },
        { id: 'item-2', title: 'Visión', content: 'Nos esforzamos por ser la librería de referencia en nuestra comunidad, siendo reconocidos por nuestra excelencia en productos, servicio al cliente y contribución al desarrollo educativo de nuestros clientes.' },
        { id: 'item-3', title: 'Valores', content: 'Excelencia: Nos comprometemos a ofrecer productos de alta calidad y servicios excepcionales en todo momento. <br>Integridad: Nos guiamos por los más altos estándares éticos y morales en todas nuestras operaciones. <br>Innovación: Buscamos constantemente formas de mejorar y adaptarnos a las necesidades cambiantes de nuestros clientes y del mercado.' },
        { id: 'item-4', title: 'Patrocinadores y socios', content: 'Trabajamos en colaboración con instituciones educativas, editoriales, proveedores de materiales escolares y empresas comprometidas con la educación para ofrecer productos de calidad y promover iniciativas que beneficien a nuestros clientes y a la comunidad en general.' },
        { id: 'item-5', title: 'Marcas', content: 'Contamos con una cuidadosa selección de marcas reconocidas por su calidad y confiabilidad en el mercado de útiles escolares y libros. Esto incluye marcas líderes en la industria que ofrecen productos innovadores y duraderos para satisfacer las necesidades de nuestros clientes.' },
        { id: 'item-6', title: 'Clientes destacados', content: 'Nuestros clientes destacados incluyen estudiantes, padres de familia, educadores y profesionales interesados en la adquisición de útiles escolares, libros de texto, material didáctico y productos relacionados con la educación. Nos esforzamos por satisfacer sus necesidades y superar sus expectativas en cada interacción.' }
    ];

    sections.forEach(section => {
        const sectionDiv = document.createElement('div');
        sectionDiv.id = section.id;

        const h4 = document.createElement('h4');
        h4.textContent = section.title;

        const p = document.createElement('p');
        p.innerHTML = section.content;

        sectionDiv.appendChild(h4);
        sectionDiv.appendChild(p);
        scrollSpyDiv.appendChild(sectionDiv);
    });

    col8Div.appendChild(scrollSpyDiv);
    rowDiv.appendChild(col8Div);

    // Agregar todo al body
    document.body.appendChild(rowDiv);
});
