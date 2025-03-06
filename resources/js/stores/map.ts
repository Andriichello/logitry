import { defineStore } from 'pinia';
import { Point, Route, Trip } from '@/api';
import L from 'leaflet';
import dayjs from 'dayjs';

export interface MapSizes {
  /** Line width in pixels */
  lineWidth: number,
  /** Marker width in pixels */
  markerWidth: number,
  /** Marker height in pixels */
  markerHeight: number,
  /** Circle radius in pixels */
  circleRadius: number,
}

export interface MapZoom {
  /** Meters per pixel */
  metersPerPixel: number | null,

  /** Scale coefficient [0 ; 1] for lines */
  lineScale: number | null,
  /** Scale coefficient [0 ; 1] for markers */
  markerScale: number | null,
  /** Scale coefficient [0 ; 1] for circles */
  circleScale: number | null,
}

export interface MapFilters {
  /** Currently selected company's 'abbreviation' */
  abbreviation: string | null,
  /** Currently selected 'from' location */
  from: string | null,
  /** Currently selected 'to' location */
  to: string | null,
  /** Currently selected beginning date */
  beg: dayjs.Dayjs | null,
  /** Currently selected end date */
  end: dayjs.Dayjs | null,
}

export interface MapState {
  /** Currently applied zoom settings */
  zoom: MapZoom;
  /** Currently applied size settings */
  sizes: MapSizes;
  /** Currently applied scaled size settings */
  scaledSizes: MapSizes;
  /** Currently applied filters */
  filters: MapFilters;

  /** Variable for handling map clicks for clearing selections */
  clicks: number;

  /** Currently selected route */
  route: Route | null;
  /** Currently selected trip */
  trip: Trip | null;
  /** Currently selected point of the route */
  point: Point | null;

  /** Determines if route points list is collapsed or not */
  arePointsHidden: boolean;
  /** Determines if filters view is shown or not */
  isShowingFilters: boolean;
}

export const useMapStore = defineStore('map', {
  state: (): MapState => {
    return {
      zoom: {
        metersPerPixel: null,
        lineScale: null,
        markerScale: null,
        circleScale: null,
      },
      sizes: {
        lineWidth: 4,
        markerWidth: 150,
        markerHeight: 150,
        circleRadius: 20,
      },
      scaledSizes: {
        lineWidth: 4,
        markerWidth: 150,
        markerHeight: 150,
        circleRadius: 20,
      },
      filters: {
        abbreviation: null,
        from: null,
        to: null,
        beg: null,
        end: null,
      },
      clicks: 0,
      route: null,
      trip: null,
      point: null,
      arePointsHidden: false,
      isShowingFilters: false,
    }
  },
  actions: {
    setFilters(filters: {abbreviation: string | null, beg: string | null, end: string | null, from: string | null, to: string | null}) {
      this.filters.abbreviation = filters.abbreviation;
      this.filters.beg = filters.beg ? dayjs(filters.beg) : null;
      this.filters.end = filters.end ? dayjs(filters.end) : null;
      this.filters.from = filters.from;
      this.filters.to = filters.to;
    },
    recalculateZoom(map: L.Map | null) {
      if (map === null) {
        this.zoom.metersPerPixel = null;
        this.zoom.lineScale = null;
        this.zoom.markerScale = null;
        this.zoom.circleScale = null;

        return;
      }

      const p1 =  map.containerPointToLatLng([0, 0])
      const p2 =  map.containerPointToLatLng([1, 0])

      const metersPerPixel = p1.distanceTo(p2);
      this.zoom.metersPerPixel = metersPerPixel;

      this.zoom.lineScale = 0.1;
      this.zoom.markerScale = 0.1;
      this.zoom.circleScale = 0.1;

      if (metersPerPixel < 1) {
        this.zoom.lineScale = 1;
        this.zoom.markerScale = 1;
        this.zoom.circleScale = 1;

        if (metersPerPixel > 0) {
          this.zoom.circleScale = 1 / metersPerPixel;
        }
      } else {
        this.zoom.circleScale = 1 / metersPerPixel;
        this.zoom.markerScale = 1 / metersPerPixel;
      }

      let scaleAtPixel = this.zoom.markerScale;

      if (metersPerPixel >= 800) {
        scaleAtPixel = 0.1;
      } else if (metersPerPixel >= 400) {
        scaleAtPixel = 0.1;
      } else if (metersPerPixel >= 200) {
        scaleAtPixel = 0.125;
      } else if (metersPerPixel >= 100) {
        scaleAtPixel = 0.15;
      } else if (metersPerPixel >= 50) {
        scaleAtPixel = 0.2;
      } else if (metersPerPixel >= 20) {
        scaleAtPixel = 0.225;
      } else if (metersPerPixel >= 15) {
        scaleAtPixel = 0.25;
      } else if (metersPerPixel >= 10) {
        scaleAtPixel = 0.275;
      } else if (metersPerPixel >= 5) {
        scaleAtPixel = 0.3;
      }

      this.zoom.lineScale = Math.max(scaleAtPixel, 1);
      this.zoom.markerScale = scaleAtPixel;
      this.zoom.circleScale = scaleAtPixel;

      if (metersPerPixel <= 100) {
        this.zoom.lineScale = Math.max(scaleAtPixel, 2);
        this.zoom.markerScale = Math.min(scaleAtPixel, 0.225);
      }

      this.scaledSizes.lineWidth = this.sizes.lineWidth * this.zoom.lineScale;
      this.scaledSizes.markerWidth = this.sizes.markerWidth * this.zoom.markerScale;
      this.scaledSizes.markerHeight = this.sizes.markerHeight * this.zoom.markerScale;
      this.scaledSizes.circleRadius = this.sizes.circleRadius * this.zoom.circleScale;
    },
  },
});
