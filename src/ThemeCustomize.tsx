
import {Button, TextInput, NavLink, Paper, Select, Tooltip} from '@mantine/core';
import customCss from './assets/css/custom.module.css'

// customize theme
export const ThemeModify:any = { 
  fontFamily: 'Roboto, sans-serif',
  focusRing:'never',
  primaryColor:'blue',
  primaryShade: 8,
  scale:1,
  colors:{
    'blue':["#eef0fe","#dae6f5","#b3cae7", "#89adda", "#6694cf", "#5084c9", "#447cc7", "#346ab1", "#2b5e9f", "#1b518d"]
  },
  components:{
    Button:Button.extend({
      classNames: {
        root: customCss["mantine-Button-root"],
        label: customCss['mantine-Button-label'],
        section: customCss['mantine-Button-section']
      },
      defaultProps:{
        size:"xs",
        variant:"filled"
      }
    }),
    Tooltip:Tooltip.extend({
      classNames: {
        tooltip: customCss["mantine-Tooltip-tooltip"],
      }
    }),
    Input:TextInput.extend({
      classNames: {
        input:customCss['mantine-Input-input'],
      },
      defaultProps:{
        size:"sm",
        placeholder:"Enter"
      }
    }),
    NavLink:NavLink.extend({
      styles: (theme) => ({
        root: {
          "--nl-bg":theme.colors[theme.primaryColor][0],
          "--nl-hover":theme.colors[theme.primaryColor][0],
        },
      }),
      classNames:{
        root:customCss['mantine-NavLink-root'],
        label:customCss['mantine-NavLink-label'],
        section:customCss['mantine-NavLink-section']
      }
    }),
    Paper:Paper.extend({
      defaultProps:{
        shadow:"0 0.125rem 0.25rem rgba(0, 0, 0, 0.075)",
        p:"8px",
        radius:0
      }
    }),
    Select:Select.extend({
      defaultProps:{
        withCheckIcon:false,
        rightSection:<i className="fa-sharp fa-solid fa-caret-down"></i>,
        rightSectionWidth:24,
        maxDropdownHeight:200,
        comboboxProps:{
          dropdownPadding: 0
        }
      },
      classNames:{
        option:customCss['mantine-Select-option'],
        section:customCss['mantine-Select-section'],
        input:customCss['mantine-Select-input'],
      }
    }),
  },
  activeClassName:'',
  headings: {
    sizes: {
      h1: {
        fontSize: '40px',
        lineHeight: 'normal',
        fontWeight: '500',
      },
      h2: {
        fontSize: '32px',
        lineHeight: 'normal',
        fontWeight: '500',
      },
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
      h6: {
        fontSize: '14px',
        lineHeight: 'normal',
        fontWeight: '500',
      },
    }
  },
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
  fontSize:{
    xs: '12px',
    sm: '14px',
    md: '16px',
    lg: '20px',
    xl: '24px',
  }
};
