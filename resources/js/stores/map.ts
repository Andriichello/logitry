import { defineStore } from 'pinia';
import { Point, Route, Trip } from '@/api';
import dayjs from 'dayjs';

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
  /** Currently selected route */
  route: Route | null;
  /** Currently selected trip */
  trip: Trip | null;
  /** Currently selected point of the route */
  point: Point | null;
  /** Variable for handling map clicks for clearing selections */
  clicks: number;
  /** Determines if route points list is collapsed or not */
  arePointsHidden: boolean;
  /** Currently applied filters */
  filters: MapFilters;
  /** Determines if filters view is shown or not */
  isShowingFilters: boolean;
}

export const useMapStore = defineStore('map', {
  state: (): MapState => {
    return {
      route: null,
      trip: null,
      point: null,
      clicks: 0,
      arePointsHidden: false,
      isShowingFilters: false,
      filters: {
        abbreviation: null,
        from: null,
        to: null,
        beg: null,
        end: null,
      },
    }
  },
  actions: {
    init(filters: {abbreviation: string | null, beg: string | null, end: string | null, from: string | null, to: string | null}) {
      this.filters.abbreviation = filters.abbreviation;
      this.filters.beg = filters.beg ? dayjs(filters.beg) : null;
      this.filters.end = filters.end ? dayjs(filters.end) : null;
      this.filters.from = filters.from;
      this.filters.to = filters.to;
    },
  },
});
