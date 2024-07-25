
import ReactDOM from 'react-dom/client';
import './assets/css/fontawesome.css';
import './assets/css/style.css';
import '@mantine/core/styles.css';

import {MantineProvider} from '@mantine/core';
import {RouterProvider} from 'react-router-dom';
import {Router} from './Router';
import {ThemeModify} from './ThemeCustomize';


const root = ReactDOM.createRoot(
  document.getElementById('root') as HTMLElement
);
root.render(
  <MantineProvider theme={ThemeModify}>
    <RouterProvider router={Router} />
  </MantineProvider>
);


