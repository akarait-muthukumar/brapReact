import Highcharts  from "highcharts"
import HighchartsReact from "highcharts-react-official"

import HightchartMore  from "highcharts/highcharts-more"
import Exporting  from "highcharts/modules/exporting"
import Exportdata  from "highcharts/modules/export-data"
import Accessibility  from "highcharts/modules/accessibility"

import type {chartPropsType } from "../../types/Dashboard";

function GaugeChart(props:chartPropsType) {

    HightchartMore(Highcharts);
    Exporting(Highcharts);
    Exportdata(Highcharts);
    Accessibility(Highcharts);

    const options = {
        chart: {
            type: 'gauge',
            plotBackgroundColor: null,
            plotBackgroundImage: null,
            plotBorderWidth: 0,
            plotShadow: false,
            height:props.height ? props.height : 180,
        },
        title: null,
        credits: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
        tooltip:{
            enabled: false
        },
        pane: {
            startAngle: -90,
            endAngle: 89.9,
            background: null,
            center: ['50%', `${props.centerY ? props.centerY : 87}%`],
            size: '180%'
        },
    
        // the value axis
        yAxis: {
            min: 0,
            max: 100,
            tickPixelInterval: 1,
            tickLength: 0,
            minorTickInterval: null,
            labels: {
                enabled: false
            },
            lineWidth: 0,
            plotBands: [{
                from: 0,
                to: 33,
                color: '#c63a3a', // green
                thickness: props.thickness ? props.thickness : 50
            }, {
                from: 33,
                to: 67,
                color: '#ffbf00', // yellow
                thickness: props.thickness ? props.thickness : 50
            }, {
                from: 67,
                to: 100,
                color: '#25682a', // red
                thickness: props.thickness ? props.thickness : 50
            }],
        },
    
        series: [{
            name: props.label ?? 'Score',
            data: [props.score],
            tooltip: {
                valueSuffix: ' %'
            },
            dataLabels: {
                format:`<div class='text-center'><h6 class='fw-bold mb-0'>${props.label ?? 'Score'} {y}%</h6></div>`,
                borderWidth: 0,
                style: {
                    fontSize: '14px'
                },
                y: 100
            },
            dial: {
                radius: '80%',
                backgroundColor: 'gray',
                baseWidth: 12,
                baseLength: '0%',
                rearLength: '0%'
            },
            pivot: {
                backgroundColor: 'gray',
                radius: 6
            }
        }]
    }
  return (
    <HighchartsReact highcharts={Highcharts} options={options} />
  )
}

export default GaugeChart