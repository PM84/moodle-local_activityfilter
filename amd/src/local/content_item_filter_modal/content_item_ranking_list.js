// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

import ContentItemRanking from "./content_item_ranking";
import {fetchMaxContentItemOccurrence, renderContentItemRankingListOn} from "./repository";

export default class ContentItemRankingList {
    /**
     * Constructor
     *
     * @param {ContentItemRanking[]} rankings List of all rankings for a use case
     */
    constructor(rankings) {
        this.rankings = rankings;
    }

    /**
     * Render the template and return the html and js
     *
     * @param {HTMLElement} target
     * @returns {Promise<void>}
     */
    async render(target) {
        return renderContentItemRankingListOn(target, await this.export());
    }

    /**
     * Export all content item ranking data
     *
     * @returns {Promise<{activityresponses: *}>} content item ranking data
     */
    async export() {
        const response = await fetchMaxContentItemOccurrence();
        if (!response.ok) {
            throw new Error(response.error);
        }

        const exportedRankings = await Promise.all(
            this.rankings.map(ranking => ranking.export(response.data))
        );

        return {
            activityresponses: exportedRankings,
        };
    }

    /**
     * Create a content item ranking list from raw data
     *
     * @param {Array} rawRankings
     * @returns {ContentItemRankingList}
     */
    static createFromRaw(rawRankings) {
        let id = 1;
        let rankings = [];

        for (const rawRanking of rawRankings) {
            rankings.push(new ContentItemRanking(
                id,
                rawRanking.pluginname,
                rawRanking.title,
                rawRanking.reason,
                rawRanking.hint,
                rawRanking.occurrences,
                rawRanking.ranking,
                rawRanking.logohtml,
            ));
            id++;
        }

        return new ContentItemRankingList(rankings);
    }
}
