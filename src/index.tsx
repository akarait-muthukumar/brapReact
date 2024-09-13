
import ReactDOM from 'react-dom/client';
import './assets/css/fontawesome.css';
import './assets/css/style.css';
import '@mantine/core/styles.css';

import AuthContext from './contextapi/AuthContext';

import {MantineProvider} from '@mantine/core';
import {BrowserRouter} from 'react-router-dom';
import {ThemeModify} from './ThemeCustomize';
import Router from './Router';

const root = ReactDOM.createRoot(
  document.getElementById('root') as HTMLElement
);

root.render(
 <BrowserRouter>
    <AuthContext>
      <MantineProvider theme={ThemeModify}>
          <Router/>
      </MantineProvider>
    </AuthContext>
  </BrowserRouter>
);


