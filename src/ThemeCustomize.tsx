
import {Button, TextInput} from '@mantine/core';
import customCss from './assets/css/custom.module.css'

// customize theme
export const ThemeModify:any = { 
  fontFamily: 'Roboto, sans-serif',
  primaryColor:'blue',
  primaryShade: 8,
  components:{
    Button:Button.extend({
      classNames: {
        root: customCss["mantine-Button-root"],
        label: customCss['mantine-Button-label'],
        section: customCss['mantine-Button-section']
      },
    }),
    Input:TextInput.extend({
      classNames: {
        input:customCss['mantine-Input-input'],
      },
    })
  },
  activeClassName:'',
  headings: {
    sizes: {
      h3: {
        fontSize: '24px',
        lineHeight: 'normal',
        fontWeight: '500',
      },
      h4: {
        fontSize: '20px',
        lineHeight: 'normal',
        fontWeight: '500',
      },
      h5: {
        fontSize: '16px',
        lineHeight: 'normal',
        fontWeight: '500',
      },
    }
  },
  globalStyles: (theme:any) => ({
    body: {
      ...theme.fn.fontStyles(),
      lineHeight:'normal'
    },
    '.nav-link.active':{
        background: theme.colors[theme.primaryColor][0],
        color:theme.colors[theme.primaryColor][9],
    },
    '.nav-link:hover':{
        color:theme.colors[theme.primaryColor][9],
    },
    '.mantine-Drawer-header, ::selection, .alertBtn' :{
      background: theme.colors[theme.primaryColor][9],
      color: 'white'
    },
    '.mantine-Table-root > thead' : {
        position:'sticky',
        top:0,
        zIndex:1,
    },
    '.mantine-Table-root > thead > tr > th':{
       color:'white',
       background: theme.colors[theme.primaryColor][9],
       border:'none !important',
       whiteSpace:'nowrap'
    },
    '.mantine-Table-root > tbody > tr:last-child':{
        borderBottom:'0.0625rem solid #dee2e6'
    },
    '.mantine-Accordion-label':{
        padding:'8px 0',
        fontSize:'14px',
        fontWeight:500,
        letterSpacing:'0.3px',
    },
    '.mantine-Accordion-control':{
        padding:'0 8px',
    },
    '.mantine-ScrollArea-thumb':{
      backgroundColor:'#0002'
    },
    '.mantine-Text-root':{
      lineHeight: 'normal'
    },
    '.mantine-InputWrapper-label':{
      fontSize:'14px',
      marginBottom:'6px',
      letterSpacing:'normal',
      lineHeight:'1.55',
      textTransform:'capitalize'
    },
  }),
  spacing:{
    xl: "32px", lg: "24px", md: "16px", sm: "8px", xs: "4px"
  },
  breakpoints: {
    xs: '30em',
    sm: '48em',
    md: '62em',
    lg: '74em',
    xl: '90em',
  },
};